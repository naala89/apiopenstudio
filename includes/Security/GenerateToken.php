<?php

/**
 * Class GenerateToken.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\LocalFileReference;

/**
 * Class GenerateToken
 *
 * Processor class to return a JWT token for a valid username/password.
 */
class GenerateToken extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Generate token',
        'machineName' => 'generate_token',
        'description' => 'Generate a JWT token. Token, uid and expires returned.',
        'menu' => 'Security',
        'input' => [
            'username' => [
                'description' => 'Users username.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'Users password.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
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
    public function process(): Core\DataContainer
    {
        parent::process();
        $username = $this->val('username', true);
        $password = $this->val('password', true);
        $userMapper = new Db\UserMapper($this->db, $this->logger);
        $userRoleMapper = new Db\UserRoleMapper($this->db, $this->logger);
        $roleMapper = new Db\RoleMapper($this->db, $this->logger);
        $config = new Config();

        // Verify User credentials.
        $this->logger->debug('api', "login attempt: $username");
        $user = $userMapper->findByUsername($username);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            // Invalid username or user inactive.
            $message = 'invalid username or password';
            $this->logger->warning('api', $message);
            throw new Core\ApiException($message, 4, $this->id, 401);
        }
        if (empty($storedHash = $user->getHash())) {
            // No password hash stored yet.
            $message = 'invalid username or password';
            $this->logger->warning('api', $message);
            throw new Core\ApiException($message, 4, $this->id, 401);
        }
        if (!Core\Hash::verifyPassword($password, $storedHash)) {
            // Invalid password.
            $message = 'invalid username or password';
            $this->logger->warning('api', $message);
            throw new Core\ApiException($message, 4, $this->id, 401);
        }

        // Get all user roles.
        $userRoles = $userRoleMapper->findByUid($user->getUid());
        $finalRoles = [];
        foreach ($userRoles as $userRole) {
            $role = $roleMapper->findByRid($userRole->getRid());
            $userRole = $userRole->dump();
            $userRole['role_name'] = $role->getName();
            unset($userRole['rid']);
            $finalRoles[] = $userRole;
        }

        try {
            $jwt_alg_type = $config->__get(['api', 'jwt_alg_type']);
            $jwt_alg = $config->__get(['api', 'jwt_alg']);
            $jwt_private_key = $config->__get(['api', 'jwt_private_key']);
            $jwt_public_key = $config->__get(['api', 'jwt_public_key']);
            $jwt_issuer = $config->__get(['api', 'jwt_issuer']);
            $jwt_permitted_for = $config->__get(['api', 'jwt_permitted_for']);
            $jwt_life = $config->__get(['api', 'jwt_life']);
        } catch (Core\ApiException $e) {
            throw new Core\ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $algorithm = "Lcobucci\\JWT\\Signer\\$jwt_alg_type\\$jwt_alg";
        if (!class_exists($algorithm)) {
            $this->logger->error('api', "Invalid algorithm path: $algorithm");
            throw new Core\ApiException('Invalid config for encryption, please check the logs', 8, $this->id, 500);
        }
        $jwtConfig = Configuration::forAsymmetricSigner(
            new $algorithm(),
            LocalFileReference::file($jwt_private_key),
            LocalFileReference::file($jwt_public_key)
        );
        $now = new DateTimeImmutable();
        $token = $jwtConfig->builder()
            ->issuedBy($jwt_issuer)
            ->permittedFor($jwt_permitted_for)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($now->modify($jwt_life))
            ->withClaim('uid', $user->getUid())
            ->withClaim('roles', $finalRoles)
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());

        return new Core\DataContainer(
            [
                'token' => $token->toString(),
                'uid' => $user->getUid(),
                'expires' => $now->modify($jwt_life)->format('d-M-y H:i:s T'),
            ],
            'array'
        );
    }
}
