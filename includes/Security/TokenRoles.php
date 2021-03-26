<?php

/**
 * Class TokenRoles.
 *
 * @package    ApiOpenStudio
 * @subpackage Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class TokenRoles
 *
 * Provide token authentication based and the user's role.
 *
 * Validation:
 *   * If user is Administrator then only against role.
 *   * If user is Account manager then against role and account.
 *   * All others against role and application.
 */
class TokenRoles extends TokenRole
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
                'limitProcessors' => [],
                'limitTypes' => ['text', 'empty'],
                'limitValues' => [],
                'default' => '',
            ],
            'roles' => [
                'description' => 'User roles that are permitted.',
                'cardinality' => [1, '*'],
                'literalAllowed' => false,
                'limitProcessors' => ['collection'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
            ],
            'validate_account' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource account. If false, the result will be if the user has the role in any accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'validate_application' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource application. If false, the result will be if the user has the role in any applications.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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
                return new Core\DataContainer(true, 'boolean');
            }
        }

        throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }
}
