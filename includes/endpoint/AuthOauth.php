<?php

/**
 * Provide cookie authentication
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

class AuthOauth extends Processor\ProcessorBase
{
  protected $required = array('consumerKey', 'nonce', 'signature', 'signatureMethod', 'accessToken', 'oauthVersion');
  protected $details = array(
    'name' => 'Auth (o-auth header)',
    'description' => 'Authentication for remote server, using o-auth signature in the header.',
    'menu' => 'Authentication',
    'application' => 'All',
    'input' => array(
      'consumerKey' => array(
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
      'accessToken' => array(
        'description' => 'The access token.',
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

    $cookie = $this->getVar($this->meta->cookie);

    return array(CURLOPT_COOKIE => $cookie);
  }
}
