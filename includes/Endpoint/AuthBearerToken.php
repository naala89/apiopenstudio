<?php

/**
 * Provide Bearer Token header authentication
 */

namespace Datagator\Endpoint;
use Datagator\Core;

class AuthBearerToken extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (Bearer token)',
    'machineName' => 'authBearerToken',
    'description' => 'Authentication for remote server, presenting a bearer token in the header.',
    'menu' => 'Authentication',
    'application' => 'Common',
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

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth (bearer token)', 4);

    $token = $this->val('token', TRUE);

    return array(CURLOPT_HTTPHEADER => "Authorization: Bearer $token");
  }
}
