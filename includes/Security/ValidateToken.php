<?php
/**
 * Class ValidateToken.
 *
 * @package    ApiOpenStudio
 * @subpackage Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ADODB_mysqli;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db;
use Lcobucci\JWT\UnencryptedToken;
use Monolog\Logger;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

/**
 * Class ValidateToken.
 *
 * Validate a JWT token.
 */
class ValidateToken extends Core\ProcessorEntity
{
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
     * @var Db\UserMapper User mapper class.
     */
    protected Db\UserMapper $userMapper;

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
     * Token constructor.
     *
     * @param mixed $meta The processor metadata.
     * @param mixed $request Request object.
     * @param ADODB_mysqli $db Database object.
     * @param Logger $logger Logger object.
     */
    public function __construct($meta, &$request, ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
    }

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

        $this->token = Core\Utilities::decryptToken();
        $this->validateToken();

        return new Core\DataContainer(true, 'boolean');
    }

    /**
     * Validate the incoming JWT token.
     *
     * @return void
     *
     * @throws Core\ApiException
     */
    protected function validateToken()
    {
        try {
            $this->uid = Core\Utilities::getUidFromToken($this->token);
            if (!assert(!empty($this->uid))) {
                throw new RequiredConstraintsViolated('invalid token');
            }
            $this->roles = Core\Utilities::getRolesFromToken($this->token);
            if (!assert(!empty($this->roles))) {
                throw new RequiredConstraintsViolated('invalid token');
            }
        } catch (RequiredConstraintsViolated $e) {
            throw new Core\ApiException($e->getMessage(), 4, $this->id, 401);
        }
    }
}
