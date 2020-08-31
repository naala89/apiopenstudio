<?php
/**
 * Class AuthBearerToken.
 *
 * @package Gaterdata
 * @subpackage Endpoint
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

/**
 * Class AuthBearerToken
 *
 * Provide Auth Bearer authentication to a resource.
 */
class AuthBearerToken extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Bearer token)',
        'machineName' => 'auth_bearer_token',
        'description' => 'Authentication for remote server, presenting a bearer token in the header.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'token' => [
                'description' => 'The token string (e.g. 907c762e069589c2cd2a229cdae7b8778caa9f07).',
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
   */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);

        return array(CURLOPT_HTTPHEADER => "Authorization: Bearer $token");
    }
}
