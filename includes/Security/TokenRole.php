<?php

/**
 * Class TokenRole.
 *
 * @package    ApiOpenStudio
 * @subpackage Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class TokenRole
 *
 * Provide token authentication based and the user's role.
 *
 * Validation:
 *   * If user is Administrator then only against role.
 *   * If user is Account manager then against role and account.
 *   * All others against role and application.
 */
class TokenRole extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Token (Role)',
        'machineName' => 'token_role',
        'description' => 'Validate that the user has a valid token and role. This is faster than Token Roles,',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'The consumers token.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'role' => [
                'description' => 'A user_role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'validate_account' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource account. If false, the result will be if the user has the role in any accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'validate_application' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource application. If false, the result will be if the user has the role in any applications.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
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
    public function process()
    {
        $this->logger->info('Security: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $validateAccount = $this->val('validate_account', true);
        $validateApplication = $this->val('validate_application', true);
        $roleName = $this->val('role', true);

        // Empty token.
        if (empty($token)) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // invalid token or user not active
        $userMapper = new Db\UserMapper($this->db);
        $user = $userMapper->findBytoken($token);
        $uid = $user->getUid();
        if (empty($uid) || $user->getActive() == 0) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // Validate user against the role.
        if ($this->validateUser($uid, $roleName, $validateAccount, $validateApplication)) {
            return true;
        }

        throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }

    /**
     * Validate a user against roles and the account/application of the resource.
     *
     * @param integer $uid User ID.
     * @param string $roleName Role name.
     * @param boolean $validateAccount Validate the user role in the account.
     * @param boolean $validateApplication Validate the user role in the application.
     *
     * @return boolean
     *
     * @throws Core\ApiException Error.
     */
    protected function validateUser(int $uid, string $roleName, bool $validateAccount, bool $validateApplication)
    {
        $roleMapper = new Db\RoleMapper($this->db);
        $role = $roleMapper->findByName($roleName);
        $rid = $role->getRid();
        if (empty($rid)) {
            throw new Core\ApiException('Invalid role defined', 4, $this->id, 401);
        }

        $userRoleMapper = new Db\UserRoleMapper($this->db);
        switch ($roleName) {
            case 'Administrator':
                $filters = [
                    'col' => [
                        'uid' => $uid,
                        'rid' => $rid
                    ]
                ];
                $userRoles = $userRoleMapper->findByFilter($filters);
                if (!empty($userRoles)) {
                    return true;
                }
                break;
            case 'Account manager':
                $filters = [
                    'col' => [
                        'uid' => $uid,
                        'rid' => $rid
                    ]
                ];
                if ($validateAccount) {
                    $filters['col']['accid'] = $this->request->getAccId();
                }
                $userRoles = $userRoleMapper->findByFilter($filters);
                if (!empty($userRoles)) {
                    return true;
                }
                break;
            default:
                $filters = [
                    'col' => [
                        'uid' => $uid,
                        'rid' => $rid
                    ]
                ];
                if ($validateAccount) {
                    $filters['col']['accid'] = $this->request->getAccId();
                }
                if ($validateApplication) {
                    $filters['col']['appid'] = $this->request->getAppId();
                }
                $userRoles = $userRoleMapper->findByFilter($filters);
                if (!empty($userRoles)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
