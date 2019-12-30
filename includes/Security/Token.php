<?php

/**
 * Provide token authentication based on token.
 */

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Db;

class Token extends Core\ProcessorEntity
{
    protected $role = false;
    /**
     * {@inheritDoc}
     */

    protected $details = [
        'name' => 'Token',
        'machineName' => 'token',
        'description' => 'Validate that the user has a valid token and roles.',
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
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Security Token', 4);
        $token = $this->val('token', true);

        // no token
        if (empty($token)) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // invalid token or user not active
        $db = $this->getDb();
        $userMapper = new Db\UserMapper($db);
        $user = $userMapper->findBytoken($token);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // get role from DB
        $roleMapper = new Db\RoleMapper($db);
        $this->role = $roleMapper->findByName($this->role);

        // return list of roles for user for this request app
        $userRoleMapper = new Db\UserRoleMapper($db);
        return $userRoleMapper->findByMixed($user->getUid(), $this->request->getAppId());
    }
}
