<?php

/**
 * Class UserRoleCreate.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Role;
use ApiOpenStudio\Db\RoleMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRole;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class UserRoleCreate
 *
 * Processor class to create a user role.
 */
class UserRoleCreate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User Role create',
        'machineName' => 'user_role_create',
        'description' => 'Create a role for a user.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'The user id of the user for the user role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'accid' => [
                'description' => 'The account ID for the user role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'The application ID for the user role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'rid' => [
                'description' => 'The user role ID for the user role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];
    /**
     * @var UserRoleMapper User Role Mapper.
     */
    protected UserRoleMapper $userRoleMapper;

    /**
     * @var UserMapper User Mapper.
     */
    protected UserMapper $userMapper;

    /**
     * @var AccountMapper
     */
    protected AccountMapper $accountMapper;

    /**
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * @var RoleMapper
     */
    protected RoleMapper $roleMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new UserRoleMapper($this->db, $this->logger);
        $this->userMapper = new UserMapper($this->db, $this->logger);
        $this->accountMapper = new AccountMapper($this->db, $this->logger);
        $this->applicationMapper = new ApplicationMapper($this->db, $this->logger);
        $this->roleMapper = new RoleMapper($this->db, $this->logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $accid = !empty($accid) ? $accid : null;
        $appid = $this->val('appid', true);
        $appid = !empty($appid) ? $appid : null;
        $rid = $this->val('rid', true);

        // Validate current user has access to create roles for the application.
        if (!empty($appid)) {
            $this->validateCurrentUserApplicationPermission($appid);
        } elseif (!empty($accid)) {
            $this->validateCurrentUserAccountPermission($accid);
        } elseif (!$this->userRoleMapper->hasRole(Utilities::getUidFromToken(), 'Administrator')) {
            throw new ApiException('permission denied', 4, $this->id, 403);
        }

        $this->validateUserRoleAttributes($uid, $rid, $accid, $appid);

        $this->validateRoleExists($uid, $rid, $accid, $appid);

        $userRole = new UserRole(null, $accid, $appid, $uid, $rid);
        $this->validateElevatedPermissions($userRole);

        // Create the new role.
        try {
            $this->userRoleMapper->save($userRole);
            $userRole = $this->userRoleMapper->findByFilter(['col' => [
                'uid' => $uid,
                'accid' => $accid,
                'appid' => $appid,
                'rid' => $rid,
            ]]);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $userRole = $userRole[0];
        return new DataContainer($userRole->dump(), 'array');
    }

    /**
     * Validate the current user has permissions to create a user/role for an application.
     *
     * @param int $appid
     *   Application ID to validate the current user against.
     *
     * @throws ApiException
     */
    protected function validateCurrentUserApplicationPermission(int $appid)
    {
        try {
            $applications = $this->applicationMapper->findByUid(
                Utilities::getUidFromToken(),
                ['col' => ['appid' => $appid]]
            );
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $permitted = false;
        foreach ($applications as $application) {
            $permitted = $application->getAppid() == $appid ? true : $permitted;
        }
        if (!$permitted) {
            throw new ApiException('permission denied for this application', 4, $this->id, 403);
        }
    }

    /**
     * Validate the current user has permissions to create a user/role for an account.
     *
     * @param int $accid
     *   Application ID to validate the current user against.
     *
     * @throws ApiException
     */
    protected function validateCurrentUserAccountPermission(int $accid)
    {
        try {
            $accounts = $this->accountMapper->findAllForUser(
                Utilities::getUidFromToken(),
                ['col' => ['accid' => $accid]]
            );
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $permitted = false;
        foreach ($accounts as $account) {
            $permitted = $account->getAccid() == $accid ? true : $permitted;
        }
        if (!$permitted) {
            throw new ApiException('permission denied for this account', 4, $this->id, 403);
        }
    }

    /**
     * Validate that the current user has sufficient privileges to assign or delete a role.
     *
     * @param UserRole $userRole
     *   User role to be created.
     *
     * @throws ApiException
     */
    protected function validateElevatedPermissions(UserRole $userRole)
    {
        $currentUid = Utilities::getUidFromToken();
        $role = $this->roleMapper->findByRid($userRole->getRid());
        $permissionGranted = false;

        switch ($role->getName()) {
            case 'Administrator':
            case 'Account manager':
                if ($this->userRoleMapper->hasRole($currentUid, 'Administrator')) {
                    $permissionGranted = true;
                }
                break;
            case 'Application manager':
                if ($this->userRoleMapper->hasRole($currentUid, 'Administrator')) {
                    $permissionGranted = true;
                }
                $application = $this->applicationMapper->findByAppid($userRole->getAppid());
                if (
                    !empty($this->userRoleMapper->findByUidAccidRolename(
                        $currentUid,
                        $application->getAccid(),
                        'Account manager'
                    ))
                ) {
                    $permissionGranted = true;
                }
                break;
            default:
                if ($this->userRoleMapper->hasRole($currentUid, 'Administrator')) {
                    $permissionGranted = true;
                }
                if (
                    !empty($this->userRoleMapper->findByUidAccidRolename(
                        $currentUid,
                        $userRole->getAppid(),
                        'Account manager'
                    ))
                ) {
                    $permissionGranted = true;
                }
                if (
                    !empty($this->userRoleMapper->findByUidAccidRolename(
                        $currentUid,
                        $userRole->getAppid(),
                        'Application manager'
                    ))
                ) {
                    $permissionGranted = true;
                }
                break;
        }
        if (!$permissionGranted) {
            throw new ApiException('Permission denied', 4, $this->id, 403);
        }
    }

    /**
     * Validate that the values for the user role exist.
     *
     * @param int $uid
     *   The user ID for the new user/role.
     * @param int $rid
     *   The role ID for the new user/role.
     * @param int|null $accid
     *   The account ID for the new user/role.
     * @param int|null $appid
     *   The application ID for the new user/role.
     *
     * @throws ApiException
     */
    protected function validateUserRoleAttributes(int $uid, int $rid, ?int $accid, ?int $appid)
    {
        try {
            $user = $this->userMapper->findByUid($uid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($user->getUid())) {
            throw new ApiException('invalid user ID', 6, $this->id, 400);
        }

        if (!is_null($accid)) {
            try {
                $account = $this->accountMapper->findByAccid($accid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($account->getAccid())) {
                throw new ApiException('invalid account ID', 6, $this->id, 400);
            }
        }

        if (!is_null($appid)) {
            try {
                $application = $this->applicationMapper->findByAppid($appid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($application->getAppid())) {
                throw new ApiException('invalid application ID', 6, $this->id, 400);
            }
            if (!empty($accid) && $application->getAccid() != $accid) {
                throw new ApiException('mismatching application & account IDs', 6, $this->id, 400);
            }
        }

        try {
            $role = $this->roleMapper->findByRid($rid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($role->getRid())) {
            throw new ApiException('invalid role ID', 6, $this->id, 400);
        }

        $this->validateNullAccidAppid($role, $accid, $appid);
    }

    /**
     * Ensure the correct null values for appid and accid are applied.
     *
     * @param Role $role
     *   The role for the new user/role.
     * @param int|null $accid
     *   The account ID for the new user/role.
     * @param int|null $appid
     *   The application ID for the new user/role.
     *
     * @throws ApiException
     */
    protected function validateNullAccidAppid(Role $role, ?int $accid, ?int $appid)
    {
        $roleName = $role->getName();
        switch ($roleName) {
            case 'Administrator':
                if (!empty($accid) || !empty($appid)) {
                    $message = 'administrator cannot be assigned an appid or accid';
                    throw new ApiException($message, 6, $this->id, 400);
                }
                break;
            case 'Account manager':
                if (empty($accid) || !empty($appid)) {
                    $message = 'account manager must be assigned to an accid only';
                    throw new ApiException($message, 6, $this->id, 400);
                }
                break;
            default:
                if (empty($accid) || empty($appid)) {
                    $message = 'this role must be assigned to an accid and appid';
                    throw new ApiException($message, 6, $this->id, 400);
                }
                break;
        }
    }

    /**
     * Validate that a role does not already exist.
     *
     * @param int $uid
     *   The user ID for the new user/role.
     * @param int $rid
     *   The role ID for the new user/role.
     * @param int|null $accid
     *   The account ID for the new user/role.
     * @param int|null $appid
     *   The application ID for the new user/role.
     *
     * @throws ApiException
     */
    protected function validateRoleExists(int $uid, int $rid, ?int $accid, ?int $appid)
    {
        try {
            $userRole = $this->userRoleMapper->findByFilter(['col' => [
                'uid' => $uid,
                'accid' => $accid,
                'appid' => $appid,
                'rid' => $rid,
            ]]);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($userRole)) {
            throw new ApiException('user role already exists', 6, $this->id, 400);
        }
    }
}
