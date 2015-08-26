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

namespace Datagator\Processors;
use Datagator\Core;

class AuthUserPwd extends Processor
{
  protected $required = array('username', 'password');
  protected $details = array(
    'name' => 'Auth (User/Pass)',
    'description' => 'Authentication for remote server, using username/password.',
    'menu' => 'authentication',
    'application' => 'All',
    'input' => array(
      'username' => array(
        'description' => 'The username.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'password' => array(
        'description' => 'The password.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth UserPwd', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $username = $this->getVar($this->meta->username);
    $password = $this->getVar($this->meta->password);

    return array(CURLOPT_USERPWD => "$username:$password");
  }
}
