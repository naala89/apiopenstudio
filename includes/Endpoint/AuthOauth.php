<?php

/**
 * Class AuthOAuth.
 *
 * @package    ApiOpenStudio\Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Endpoint;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;

/**
 * Class AuthOAuth
 *
 * Provide OAuth authentication to a resource.
 */
class AuthOauth extends ProcessorEntity
{
  /**
   * {@inheritDoc}
   *
   * @var array Details of the processor.
   */
    protected array $details = [
        'name' => 'Auth (OAuth)',
        'machineName' => 'auth_oauth',
        'description' => 'Authentication for remote server, using OAuth signature in the header.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'key' => [
                'description' => 'The consumer key.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'nonce' => [
                'description' => 'The nonce.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'signature' => [
                'description' => 'The signature.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'signatureMethod' => [
                'description' => 'The signature method.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'HMAC-SHA1',
            ],
            'oauthVersion' => [
                'description' => 'The OAuth version.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '1.0',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        parent::process();

        $headers = array(
            'Authorization' => 'OAuth',
            'oauth_consumer_key' => $this->val('key', true),
            'oauth_nonce' => $this->val('nonce', true),
            'oauth_signature' => $this->val('signature', true),
            'oauth_signature_method' => $this->val('signatureMethod', true),
            'oauth_timestamp' => time(),
            'oauth_version' => $this->val('oauthVersion', true)
        );

        return new DataContainer([CURLOPT_HTTPHEADER => $headers], 'array');
    }
}
