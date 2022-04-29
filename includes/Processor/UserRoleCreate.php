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
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db;

/**
 * Class UserRoleCreate
 *
 * Processor class to create a user role.
 */
class UserRoleCreate extends Core\ProcessorEntity
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
                'description' => 'The user id of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'accid' => [
                'description' => 'The account ID of user roles.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application ID of user roles.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'rid' => [
                'description' => 'The user role ID of user roles.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * @var Db\UserRoleMapper User Role Mapper.
     */
    private Db\UserRoleMapper $userRoleMapper;

    /**
     * @var Db\UserMapper User Mapper.
     */
    private Db\UserMapper $userMapper;
    private Db\AccountMapper $accountMapper;
    private Db\ApplicationMapper $applicationMapper;
    private Db\RoleMapper $roleMapper;

    /**
     * @param $meta
     * @param Request $request
     * @param ADOConnection|null $db
     * @param MonologWrapper|null $logger
     */
    public function __construct($meta, Request &$request, ADOConnection $db = null, MonologWrapper $logger = null)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db, $this->logger);
        $this->userMapper = new Db\UserMapper($this->db, $this->logger);
        $this->accountMapper = new Db\AccountMapper($this->db, $this->logger);
        $this->applicationMapper = new Db\ApplicationMapper($this->db, $this->logger);
        $this->roleMapper = new Db\RoleMapper($this->db, $this->logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $accid = !empty($accid) ? $accid : null;
        $appid = $this->val('appid', true);
        $appid = !empty($appid) ? $appid : null;
        $rid = $this->val('rid', true);

        // Validate user role attributes exist.
        try {
            $user = $this->userMapper->findByUid($uid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($user->getUid())) {
            throw new Core\ApiException('invalid user ID', 6, $this->id, 400);
        }
        if ($accid !== null) {
            try {
                $account = $this->accountMapper->findByAccid($accid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($account->getAccid())) {
                throw new Core\ApiException('invalid account ID', 6, $this->id, 400);
            }
        }
        if ($appid !== null) {
            try {
                $application = $this->applicationMapper->findByAppid($appid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($application->getAppid())) {
                throw new Core\ApiException('invalid application ID', 6, $this->id, 400);
            }
        }
        try {
            $role = $this->roleMapper->findByRid($rid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($role->getRid())) {
            throw new Core\ApiException('invalid role ID', 6, $this->id, 400);
        }

        // Validate roles that do not need appid or accid
        $roleName = $role->getName();
        if ($roleName != 'Administrator' && $roleName != 'Account manager' && empty($appid)) {
            $message = 'only Administrator or Account manager roles can have NULL assigned to application';
            throw new Core\ApiException($message, 6, $this->id, 400);
        }
        if ($roleName != 'Administrator' && empty($accid)) {
            throw new Core\ApiException('only Administrator role can have NULL assigned to account', 6, $this->id, 400);
        }
        if ($roleName == 'Administrator' || $roleName == 'Account manager') {
            // Administrator or Account manager should not be assigned an appid.
            $appid = null;
        }
        if ($roleName == 'Administrator') {
            // Administrator should not be assigned an accid.
            $accid = null;
        }

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
            throw new Core\ApiException('user role already exists', 6, $this->id, 400);
        }

        $userRole = new Db\UserRole(null, $accid, $appid, $uid, $rid);
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
        return new Core\DataContainer($userRole->dump(), 'array');
    }
}
