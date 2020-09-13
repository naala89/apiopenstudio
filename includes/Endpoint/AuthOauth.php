<?php
/**
 * Class AuthOAuth.
 *
 * @package Gaterdata
 * @subpackage Endpoint
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

/**
 * Class AuthOAuth
 *
 * Provide OAuth authentication to a resource.
 */
class AuthOAuth extends Core\ProcessorEntity
{
  /**
   * @var array Details of the processor.
   *
   * {@inheritDoc}
   */
    protected $details = [
        'name' => 'Auth (OAuth)',
        'machineName' => 'auth_oauth',
        'description' => 'Authentication for remote server, using OAuth signature in the header.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'key' => [
                'description' => 'The consumer key.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'nonce' => [
                'description' => 'The nonce.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'signature' => [
                'description' => 'The signature.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'signatureMethod' => [
                'description' => 'The signature method.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'HMAC-SHA1',
            ],
            'oauthVersion' => [
                'description' => 'The OAuth version.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '1.0',
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

        $headers = array(
        Authorization => OAuth,
        'oauth_consumer_key' => $this->val('key', true),
        'oauth_nonce' => $this->val('nonce', true),
        'oauth_signature' => $this->val('signature', true),
        'oauth_signature_method' => $this->val('signatureMethod', true),
        'oauth_timestamp' => time(),
        'oauth_version' => $this->val('oauthVersion', true)
        );

        return array(CURLOPT_HTTPHEADER => $headers);
    }
}
