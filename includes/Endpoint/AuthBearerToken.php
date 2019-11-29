<?php

/**
 * Provide Bearer Token header authentication
 */

namespace Gaterdata\Endpoint;
use Gaterdata\Core;

class AuthBearerToken extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = array(
    'name' => 'Auth (Bearer token)',
    'machineName' => 'auth_bearer_token',
    'description' => 'Authentication for remote server, presenting a bearer token in the header.',
    'menu' => 'Authentication',
    'input' => array(
      'token' => array(
        'description' => 'The token string (e.g. 907c762e069589c2cd2a229cdae7b8778caa9f07).',
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
    Core\Debug::variable($this->meta, 'Auth (bearer token)', 4);

    $token = $this->val('token', TRUE);

    return array(CURLOPT_HTTPHEADER => "Authorization: Bearer $token");
  }
}
