<?php

/**
 * Provide Basic username/Password authentication
 */

namespace Datagator\Endpoint;
use Datagator\Core;

class AuthBasic extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (Basic User/Pass)',
    'machineName' => 'authBasic',
    'description' => 'Basic authentication for remote server, using username/password.',
    'menu' => 'Authentication',
    'application' => 'Common',
    'input' => array(
      'username' => array(
        'description' => 'The username.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'password' => array(
        'description' => 'The password.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth Basic', 4);

    $username = $this->val('username', true);
    $password = $this->val('password', true);

    return array(CURLOPT_USERPWD => "$username:$password");
  }
}
