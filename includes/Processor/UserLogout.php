<?php

/**
 * Class UserLogout.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class UserLogout
 *
 * Processor class to perform a logout operation.
 */
class UserLogout extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'User Logout',
        'machineName' => 'user_logout',
        'description' => 'Logout a user. Inputs are login token, uid or username. Booolean response is the result.',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'Users current login token.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uid' => [
                'description' => 'Users ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'username' => [
                'description' => 'Username.',
                'cardinality' => [0, 1],
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
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        if (empty($token) && empty($uid) && empty($username)) {
            throw new Core\ApiException('Invalid logout attempt, no input values.', 4, $this->id, 401);
        }
        $userMapper = new Db\UserMapper($this->db);

        // Find the user.
        if (!empty($token)) {
            $this->logger->debug("Logging out user with token: $token.");
            $user = $userMapper->findBytoken($token);
        } elseif (!empty($uid)) {
            $this->logger->debug("Logging out user with UID: $uid.");
            $user = $userMapper->findByUid($uid);
        } else {
            $this->logger->debug("Logging out user with username: $username.");
            $user = $userMapper->findByUsername($username);
        }
        if (empty($user->getUid())) {
            $message = "Logout: user not found";
            throw new Core\ApiException($message, 4, $this->id, 401);
            $this->logger->warning($message);
        }

        // Perform logout.
        $user->setToken('');
        $user->setTokenTtl(Core\Utilities::mysqlNow());

        return new Core\DataContainer(
            $result = $userMapper->save($user),
            'boolean'
        );
    }
}
