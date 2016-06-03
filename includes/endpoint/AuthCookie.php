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

class AuthCookie extends Processor\ProcessorBase
{
  protected $details = array(
    'name' => 'Auth (Cookie)',
    'description' => 'Authentication for remote server, using a cookie.',
    'menu' => 'Authentication',
    'application' => 'All',
    'input' => array(
      'cookie' => array(
        'description' => 'The cookie.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth Cookie', 4);

    $cookie = $this->val($this->meta->cookie);

    return array(CURLOPT_COOKIE => $cookie);
  }
}
