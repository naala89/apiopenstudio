<?php

/**
 * Class UserRoleCreate.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
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

        if ($rid > 2 && empty($appid)) {
            $message = 'Only Administrator or Account manager roles can have NULL assigned to application';
            throw new Core\ApiException($message, 6, $this->id, 400);
        }
        if ($rid > 1 && empty($accid)) {
            throw new Core\ApiException('Only Administrator role can have NULL assigned to account', 6, $this->id, 400);
        }
        if ($rid < 3) {
            // Administrator or Account manager should not be assigned an appid.
            $appid = null;
        }
        if ($rid < 2) {
            // Administrator should not be assigned an accid.
            $accid = null;
        }

        $userRoleMapper = new Db\UserRoleMapper($this->db, $this->logger);

        $userRole = $userRoleMapper->findByFilter(['col' => [
            'uid' => $uid,
            'accid' => $accid,
            'appid' => $appid,
            'rid' => $rid,
        ]]);
        if (!empty($userRole)) {
            throw new Core\ApiException('User role already exists', 6, $this->id, 400);
        }

        $userRole = new Db\UserRole(null, $accid, $appid, $uid, $rid);
        return new Core\DataContainer($userRoleMapper->save($userRole), 'bool');
    }
}
