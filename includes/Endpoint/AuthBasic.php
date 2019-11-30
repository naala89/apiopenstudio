<?php

/**
 * Provide Basic username/Password authentication
 */

namespace Gaterdata\Endpoint;
use Gaterdata\Core;

class AuthBasic extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
    protected $details = array(
    'name' => 'Auth (Basic User/Pass)',
    'machineName' => 'auth_basic',
    'description' => 'Basic authentication for remote server, using username/password.',
    'menu' => 'Authentication',
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

  /**
   * {@inheritDoc}
   */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Auth Basic', 4);

        $username = $this->val('username', true);
        $password = $this->val('password', true);

        return array(CURLOPT_USERPWD => "$username:$password");
    }
}
