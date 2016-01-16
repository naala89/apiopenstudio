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

class AuthOAuthHeader extends Processor\ProcessorBase
{
  protected $required = array('key', 'nonce', 'signature', 'signatureMethod', 'oauthVersion');
  protected $details = array(
    'name' => 'Auth (o-auth header)',
    'description' => 'Authentication for remote server, using OAuth signature in the header.',
    'menu' => 'Authentication',
    'application' => 'All',
    'input' => array(
      'key' => array(
        'description' => 'The consumer key.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'nonce' => array(
        'description' => 'The nonce.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'signature' => array(
        'description' => 'The signature.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'signatureMethod' => array(
        'description' => 'The signature method.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'oauthVersion' => array(
        'description' => 'The o-auth version.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth o-auth(header)', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $key = $this->getVar($this->meta->key);
    $nonce = $this->getVar($this->meta->nonce);
    $signature = $this->getVar($this->meta->signature);
    $signatureMethod = $this->getVar($this->meta->signatureMethod);
    $timestamp = time();
    $oauthVersion = $this->getVar($this->meta->oauthVersion);

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
