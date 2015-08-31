<?php

/**
 * Provide token authentication based on token in DB
 *
 * Meta:
 *    {
 *      "type": "token",
 *      "meta": {
 *        "id":<integer>,
 *        "token": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Processors;
use Datagator\Config;
use Datagator\Core;

class UserLogin extends ProcessorBase {

  protected $required = array('username', 'password');
  public $details = array(
    'name' => 'User Login',
    'description' => 'Login a user for token-based API access.',
    'menu' => 'Validator',
    'application' => 'All',
    'input' => array(
      'username' => array(
        'description' => 'Users username.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'password' => array(
        'description' => 'Users password.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  /**
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Processor UserLogin', 4);
    $this->validateRequired();

    // validate username and active status
    $user = $this->request->user->findByUsername($this->getVar($this->meta->username));
    if (!$this->request->user->exists() || !$this->request->user->isActive()) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    // set up salt if not defined
    if ($user->getSalt() == null) {
      $user->setSalt(Core\Hash::generateSalt());
    }

    // generate hash and compare
    $hash = Core\Hash::generateHash($this->getVar($this->meta->password), $user->getSalt());
    if ($user->getHash() != null && $user->getHash() != $hash) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    //perform login and return token
    $user->setHash($hash);
    $tokenString = time() . $user->getUsername();
    $token = md5($tokenString);
    $user->setToken($token);
    $user->setTokenTtl(Core\Utilities::date_php2mysql(strtotime(Config::$tokenLife)));
    $this->request->user->setUser($user);
    $this->request->user->save();
    return $token;
  }
}
