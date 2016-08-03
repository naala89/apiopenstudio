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

class AuthCookie extends Processor\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (Cookie)',
    'machineName' => 'authCookie',
    'description' => 'Authentication for remote server, using a cookie.',
    'menu' => 'Authentication',
    'application' => 'Common',
    'input' => array(
      'cookie' => array(
        'description' => 'The cookie.',
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
    Core\Debug::variable($this->meta, 'Auth Cookie', 4);

    $cookie = $this->val('cookie');

    return array(CURLOPT_COOKIE => $cookie);
  }
}
