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
  protected $details = array(
    'name' => 'Auth (OAuth)',
    'machineName' => 'auth_oauth',
    'description' => 'Authentication for remote server, using OAuth signature in the header.',
    'menu' => 'Authentication',
    'input' => array(
      'key' => array(
        'description' => 'The consumer key.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nonce' => array(
        'description' => 'The nonce.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'signature' => array(
        'description' => 'The signature.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'signatureMethod' => array(
        'description' => 'The signature method.',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => 'HMAC-SHA1'
      ),
      'oauthVersion' => array(
        'description' => 'The OAuth version.',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => '1.0'
      ),
    ),
  );

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth o-auth(header)', 4);

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
