<?php

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * Provide token authentication based on token and the multiple user roles.
 */

class TokenRoles extends TokenRole
{
  /**
   * {@inheritDoc}
   */
    protected $details = [
        'name' => 'Token (Roles)',
        'machineName' => 'token_roles',
        'description' => 'Validate that the user has a valid token and roles.',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'The users token.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'empty'],
                'limitValues' => [],
                'default' => '',
            ],
            'roles' => [
                'description' => 'User roles that are permitted.',
                'cardinality' => [1, '*'],
                'literalAllowed' => false,
                'limitFunctions' => ['collection'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
            ],
            'validate_account' => [
                'description' => 'Validate The has has the role in the resource account. If false, the result will be if the user has the role in any accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'validate_application' => [
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
     */
    public function process()
    {
        $this->logger->info('Security: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $validateAccount = $this->val('validate_account', true);
        $validateApplication = $this->val('validate_application', true);

        // no token
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

        // Get roles and validate the user.
        $roleNames = $this->val('roles', true);
        foreach ($roleNames as $roleName) {
            if ($this->validateUser($uid, $roleName, $validateAccount, $validateApplication) == true) {
                return true;
            }
        }

        throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }
}
