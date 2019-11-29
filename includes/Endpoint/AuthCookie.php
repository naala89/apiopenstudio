<?php

/**
 * Provide cookie authentication
 */

namespace Gaterdata\Endpoint;
use Gaterdata\Core;

class AuthCookie extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = array(
    'name' => 'Auth (Cookie)',
    'machineName' => 'auth_cookie',
    'description' => 'Authentication for remote server, using a cookie.',
    'menu' => 'Authentication',
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

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth Cookie', 4);

    $cookie = $this->val('cookie', true);

    return array(CURLOPT_COOKIE => $cookie);
  }
}
