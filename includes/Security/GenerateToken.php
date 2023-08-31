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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\Hash;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Db;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\LocalFileReference;

/**
 * Class GenerateToken
 *
 * Processor class to return a JWT token for a valid username/password.
 */
class GenerateToken extends ProcessorEntity
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
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
            throw new ApiException($message, 4, $this->id, 401);
        }
        if (empty($storedHash = $user->getHash())) {
            // No password hash stored yet.
            $message = 'invalid username or password';
            $this->logger->warning('api', $message);
            throw new ApiException($message, 4, $this->id, 401);
        }
        if (!Hash::verifyPassword($password, $storedHash)) {
            // Invalid password.
            $message = 'invalid username or password';
            $this->logger->warning('api', $message);
            throw new ApiException($message, 4, $this->id, 401);
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
            $refresh_token_life = $config->__get(['api', 'refresh_token_life']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $algorithm = "Lcobucci\\JWT\\Signer\\$jwt_alg_type\\$jwt_alg";
        if (!class_exists($algorithm)) {
            $this->logger->error('api', "Invalid algorithm path: $algorithm");
            throw new ApiException('Invalid config for encryption, please check the logs', 8, $this->id, 500);
        }
        $jwtConfig = Configuration::forAsymmetricSigner(
            new $algorithm(),
            LocalFileReference::file($jwt_private_key),
            LocalFileReference::file($jwt_public_key)
        );
        $now = new DateTimeImmutable();
        $jwtExpiry = $now->modify($jwt_life);
        $refreshExpiry = $now->modify($refresh_token_life);
        $builder = $jwtConfig->builder();
        $token = $builder
            ->issuedBy($jwt_issuer)
            ->permittedFor($jwt_permitted_for)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($jwtExpiry)
            ->withClaim('uid', $user->getUid())
            ->withClaim('roles', $finalRoles)
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());
        $refreshToken = $builder
            ->issuedBy($jwt_issuer)
            ->permittedFor($jwt_permitted_for)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($refreshExpiry)
            ->withClaim('uid', $user->getUid())
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());
        $refreshToken = $refreshToken->toString();
        $user->setRefreshToken($refreshToken);
        $userMapper->save($user);

        $array = [
            'uid' => $user->getUid(),
            'token' => $token->toString(),
            'token_expiry' => $jwtExpiry->format('d-M-y H:i:s T'),
            'refresh_token' => $refreshToken,
            'refresh_expiry' => $refreshExpiry->format('d-M-y H:i:s T'),
        ];
        return new DataContainer($array, 'array');
    }
}
