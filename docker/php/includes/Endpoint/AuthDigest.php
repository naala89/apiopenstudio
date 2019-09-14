<?php

/**
 * Provide Digest username/ Password authentication
 */

namespace Gaterdata\Endpoint;
use Gaterdata\Core;

class AuthDigest extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (Digest User/Pass)',
    'machineName' => 'authDigest',
    'description' => 'Digest authentication for remote server, using username/password.',
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
    Core\Debug::variable($this->meta, 'Auth Digest', 4);

    $username = $this->val('username', true);
    $password = $this->val('password', true);

    return array(
      CURLOPT_USERPWD => "$username:$password",
      CURLOPT_HTTPAUTH => CURLAUTH_DIGEST
    );
  }
}
