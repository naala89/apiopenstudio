<?php

/**
 * Provide OAuth header authentication
 *
 * This class is to be used by ProcessorInput.
 *
 * Meta:
 *    {
 *      "type": "cookie",
 *      "meta": {
 *        "id": <integer>,
 *        "cookie": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;

class AuthOAuthHeader extends Processor\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (o-auth header)',
    'machineName' => 'authOAuthHeader',
    'description' => 'Authentication for remote server, using OAuth signature in the header.',
    'menu' => 'Authentication',
    'application' => 'Common',
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
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'oauthVersion' => array(
        'description' => 'The OAuth version.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth o-auth(header)', 4);

    $key = $this->val('key');
    $nonce = $this->val('nonce');
    $signature = $this->val('signature');
    $signatureMethod = $this->val('signatureMethod');
    $timestamp = time();
    $oauthVersion = $this->val('oauthVersion');

    $header = 'OAuth ';
    $header .= !empty($key) ? "oauth_consumer_key=$key" : '';
    $header .= !empty($nonce) ? "oauth_nonce=$nonce" : '';
    $header .= !empty($signature) ? "oauth_signature=$signature" : '';
    $header .= !empty($signatureMethod) ? "oauth_signature_method=$signatureMethod" : '';
    $header .= !empty($timestamp) ? "oauth_timestamp=$timestamp" : '';
    $header .= !empty($oauthVersion) ? "oauth_version=$oauthVersion" : '';

    return array('Authorization' => $header);
  }
}
