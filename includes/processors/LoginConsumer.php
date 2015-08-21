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

class LoginConsumer extends ProcessorBase {

  protected $required = array('username', 'password');
  protected $details = array(
    'name' => 'LoginConsumer',
    'description' => 'Login as a user with role "consumer" for token-based API access.',
    'menu' => 'api access',
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
    Core\Debug::message('Validator LoginConsumer', 4);
    $this->validateRequired();

    $username = $this->getVar($this->meta->username);
    $password = $this->getVar($this->meta->password);
    $userObj = new Core\User($this->request->db);

    $user = $userObj->findByUsername($username);
    if (empty($user->getUid()) || !$user->getActive()) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }
    if ($user->getSalt() == null) {
      $user->setSalt(Core\Hash::generateSalt());
    }
    $hash = Core\Hash::generateHash($password, $user->getSalt());
    if ($user->getHash() != null && $user->getHash() != $hash) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    $user->setHash($hash);
    $tokenString = time() . $user->getUsername();
    $token = md5($tokenString);
    $user->setToken($token);
    $user->setTokenTtl(Core\Utilities::date_php2mysql(time() + Config::$tokenLife));
    $userObj->setUser($user);
    $userObj->save();

    return $token;
  }
}
