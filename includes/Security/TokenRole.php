<?php

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Db;

/**
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
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Security TokenRole', 4);

        // no token
        $token = $this->val('token', true);
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
        $roleName = $this->val('role', true);
        if ($this->validateUser($uid, $roleName)) {
            return true;
        }
        throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }

    /**
     * Validate a user against roles and the account/application of the resource.
     *
     * @param $uid
     *   User ID
     * @param $roleName
     *   Role name.
     *
     * @return bool
     *
     * @throws Core\ApiException
     */
    protected function validateUser($uid, $roleName)
    {
        $roleMapper = new Db\RoleMapper($this->db);
        $role = $roleMapper->findByName($roleName);
        if (empty($rid = $role->getRid())) {
            throw new Core\ApiException('Invalid role defined', 4, $this->id, 401);
        }

        $userRoleMapper = new Db\UserRoleMapper($this->db);
        switch ($roleName) {
            case 'Administrator':
                $userRoles = $userRoleMapper->findByFilter([
                    'col' => [
                        'uid' => $uid,
                        'rid' => $rid
                    ]
                ]);
                if (!empty($userRoles)) {
                    return true;
                }
                break;
            case 'Account manager':
                $userRoles = $userRoleMapper->findByFilter([
                    'col' => [
                        'uid' => $uid,
                        'accid' => $this->request->getAccId(),
                        'rid' => $rid
                    ]
                ]);
                if (!empty($userRoles)) {
                    return true;
                }
                break;
            default:
                $userRoles = $userRoleMapper->findByFilter([
                    'col' => [
                        'uid' => $uid,
                        'appid' => $this->request->getAppId(),
                        'rid' => $rid
                    ]
                ]);
                if (!empty($userRoles)) {
                    return true;
                }
        }

        return false;
    }
}
