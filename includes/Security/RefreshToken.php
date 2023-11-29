<?php

/**
 * Class RefreshToken.
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
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db;
use ApiOpenStudio\Db\UserMapper;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

/**
 * Class RefreshToken
 *
 * Processor class to return a JWT token with a valid token and refresh token.
 */
class RefreshToken extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Refresh token',
        'machineName' => 'refresh_token',
        // phpcs:ignore
        'description' => 'Generate a JWT token and refresh token based on a valid token and refresh. Token, refresh token, uid and expiry times returned.',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'Request token (this is assumed to be stale).',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'refresh_token' => [
                'description' => 'Refresh token.',
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
     * @var int $uid User ID.
     */
    protected int $uid;
    /**
     * @var UnencryptedToken $token Decrypted token.
     */
    protected UnencryptedToken $token;
    /**
     * @var UnencryptedToken $refreshToken Decrypted refresh_token.
     */
    protected UnencryptedToken $refreshToken;
    /**
     * @var UserMapper User mapper class.
     */
    protected UserMapper $userMapper;

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
        $token = $this->val('token', true);
        $refreshToken = $this->val('refresh_token', true);

        // Decrypt the tokens
        try {
            $this->token = Utilities::decryptToken($token);
            $this->refreshToken = Utilities::decryptToken($refreshToken);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        // Ensure there is a matching uid in the tokens.
        try {
            $this->uid = Utilities::getClaimFromToken('uid', $this->token);
            $uid = Utilities::getClaimFromToken('uid', $this->refreshToken);
            $refresh_expiry = Utilities::getClaimFromToken('exp', $this->refreshToken);
            $now = date("d-M-y H:i:s T");
            if (!assert(!empty($this->uid)) || $this->uid != $uid || $refresh_expiry < $now) {
                throw new RequiredConstraintsViolated('invalid refresh token');
            }
        } catch (RequiredConstraintsViolated $e) {
            $this->logger->warning('api', $e->getMessage());
            throw new ApiException($e->getMessage(), 4, $this->id, 401);
        }

        // Find the user.
        $userMapper = new Db\UserMapper($this->db, $this->logger);
        $user = $userMapper->findByUid($this->uid);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            $message = 'invalid refresh token';
            $this->logger->warning('api', $message);
            throw new ApiException($message, 4, $this->id, 401);
        }

        $finalRoles = $this->getAllUserRoles($user);

        //Generate tokens.
        $result = $this->generateTokens($user, $finalRoles);
        $user->setRefreshToken($result['refresh_token']);
        $userMapper->save($user);

        return new DataContainer($result, 'array');
    }

    /**
     * Generate an auth token result array.
     *
     * @param Db\User $user The user.
     * @param array $finalRoles The roles for the user.
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function generateTokens(Db\User $user, array $finalRoles): array
    {
        // Get configs
        $config = new Config();
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
            $this->logger->error('api', $e->getMessage());
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

        return [
            'uid' => $user->getUid(),
            'token' => $token->toString(),
            'token_expiry' => $jwtExpiry->format('F d, Y H:i:s T'),
            'refresh_token' => $refreshToken->toString(),
            'refresh_expiry' => $refreshExpiry->format('F d, Y H:i:s T'),
        ];
    }

    /**
     * Find all the user roles for a user.
     *
     * @param Db\User $user The user object.
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getAllUserRoles(Db\User $user): array
    {
        $userRoleMapper = new Db\UserRoleMapper($this->db, $this->logger);
        $roleMapper = new Db\RoleMapper($this->db, $this->logger);

        $userRoles = $userRoleMapper->findByUid($user->getUid());
        $finalRoles = [];
        foreach ($userRoles as $userRole) {
            $role = $roleMapper->findByRid($userRole->getRid());
            $userRole = $userRole->dump();
            $userRole['role_name'] = $role->getName();
            unset($userRole['rid']);
            $finalRoles[] = $userRole;
        }

        return $finalRoles;
    }
}
