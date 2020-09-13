<?php
/**
 * Class UserLogin.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core\Config;
use Gaterdata\Core;
use Gaterdata\Db;

/**
 * Class UserLogin
 *
 * Processor class to perform a login operation.
 */
class UserLogin extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'User Login',
        'machineName' => 'user_login',
        'description' => 'Login a user. Token and uid returned.',
        'menu' => 'Security',
        'input' => [
            'username' => [
                'description' => 'Users username.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'Users password.',
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
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $username = $this->val('username', true);
        $password = $this->val('password', true);
        $userMapper = new Db\UserMapper($this->db);

        // Validate username and active status.
        $this->logger->debug("login attempt: $username");
        $user = $userMapper->findByUsername($username);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            $message = 'invalid username or password';
            throw new Core\ApiException($message, 4, $this->id, 401);
            $this->logger->warning($message);
        }

        // No password hash stored yet.
        if (empty($storedHash = $user->getHash())) {
            $message = 'invalid username or password';
            throw new Core\ApiException($message, 4, $this->id, 401);
            $this->logger->warning($message);
        }
        $hash = Core\Hash::generateHash($password);
        if (!Core\Hash::verifPassword($password, $storedHash)) {
            $message = 'invalid username or password';
            throw new Core\ApiException($message, 4, $this->id, 401);
            $this->logger->warning($message);
        }

        // if token exists and is active, return it
        $config = new Config();
        $tokenLife = $config->__get(['api', 'token_life']);
        if (!empty($user->getToken())
            && !empty($user->getTokenTtl())
            && Core\Utilities::dateMysql2php($user->getTokenTtl()) > time()
        ) {
            $user->setTokenTtl(Core\Utilities::datePhp2mysql(strtotime($tokenLife)));
            $userMapper->save($user);
            return new Core\DataContainer(
                ['token' => $user->getToken(), 'uid' => $user->getUid()],
                'array'
            );
        }

        //perform login
        $user->setHash($hash);
        $token = Core\Hash::generateToken($username);
        $user->setToken($token);
        $user->setTokenTtl(Core\Utilities::datePhp2mysql(strtotime($tokenLife)));
        $userMapper->save($user);

        return new Core\DataContainer(
            ['token' => $user->getToken(), 'uid' => $user->getUid()],
            'array'
        );
    }
}
