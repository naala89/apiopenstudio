<?php

/**
 * Provide cookie authentication
 */

namespace Datagator\Endpoint;
use Datagator\Core;

class AuthCookie extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (Cookie)',
    'machineName' => 'authCookie',
    'description' => 'Authentication for remote server, using a cookie.',
    'menu' => 'Authentication',
    'application' => 'Common',
    'input' => array(
      'cookie' => array(
        'description' => 'The cookie string.',
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

    $cookie = $this->val('cookie', true);

    return array(CURLOPT_COOKIE => $cookie);
  }
}
