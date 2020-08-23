<?php

/**
 * Provide OAuth header authentication
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

class AuthOAuth extends Core\ProcessorEntity
{
  /**
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
