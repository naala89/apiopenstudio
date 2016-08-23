<?php

/**
 * Provide username/ Password authentication
 *
 * this class is to be used by ProcessorInput.
 * The map parameters allow user/pass var names to be mapped to different get vars
 *
 * Meta:
 *    {
 *      "type": "userPass",
 *      "meta": {
 *        "id": <integer>,
 *        "username": <obj|string>,
 *        "password": <obj|string>
 *      }
 *    }
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;

class AuthUserPwd extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Auth (User/Pass)',
    'machineName' => 'authUserPwd',
    'description' => 'Authentication for remote server, using username/password.',
    'menu' => 'Authentication',
    'application' => 'Common',
    'input' => array(
      'username' => array(
        'description' => 'The username.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'password' => array(
        'description' => 'The password.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
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
    Core\Debug::variable($this->meta, 'Auth UserPwd', 4);

    $username = $this->val('username');
    $password = $this->val('password');

    return array(CURLOPT_USERPWD => "$username:$password");
  }
}
