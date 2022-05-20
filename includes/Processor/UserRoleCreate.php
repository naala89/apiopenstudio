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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\UserRole;

/**
 * Class UserRoleCreate
 *
 * Processor class to create a user role.
 */
class UserRoleCreate extends UserRoleBase
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
}
