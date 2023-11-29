<?php

/**
 * Class ValidateToken.
 *
 * @package    ApiOpenStudio\Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\UserMapper;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

/**
 * Class ValidateToken.
 *
 * Validate a JWT token.
 */
class ValidateToken extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Validate Token',
        'machineName' => 'validate_token',
        'description' => 'Validate that the user has a valid JWT token.',
        'menu' => 'Security',
        'input' => [],
    ];

    /**
     * @var int $uid User ID.
     */
    protected int $uid;
    /**
     * @var array $roles User roles.
     */
    protected array $roles;
    /**
     * @var UnencryptedToken $token Decrypted token.
     */
    protected UnencryptedToken $token;
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

        try {
            $this->token = Utilities::decryptToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $this->validateToken();

        return new DataContainer(true, 'boolean');
    }

    /**
     * Validate the incoming JWT token.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateToken()
    {
        try {
            $this->uid = Utilities::getClaimFromToken('uid', $this->token);
            if (!assert(!empty($this->uid))) {
                throw new RequiredConstraintsViolated('invalid token');
            }
            $this->roles = Utilities::getClaimFromToken('roles', $this->token);
            if (!assert(!empty($this->roles))) {
                throw new RequiredConstraintsViolated('invalid token');
            }
        } catch (RequiredConstraintsViolated $e) {
            throw new ApiException($e->getMessage(), 4, $this->id, 401);
        }
    }
}
