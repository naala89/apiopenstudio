<?php

/**
 * Provide token authentication based on token in DB
 */

namespace Datagator\Processor;
use Datagator\Config;
use Datagator\Core;
use Datagator\Db;

class UserLogin extends ProcessorEntity
{
  protected $details = array(
    'name' => 'User Login',
    'description' => 'Login a user for token-based API access.',
    'menu' => 'Validator',
    'application' => 'Common',
    'input' => array(
      'username' => array(
        'description' => 'Users username.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'password' => array(
        'description' => 'Users password.',
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
   * @return mixed|string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Processor UserLogin', 4);

    $username = $this->val($this->meta->username);
    $password = $this->val($this->meta->password);
    $db = $this->getDb();
    $userMapper = new Db\UserMapper($db);

    // validate username and active status
    $user = $userMapper->findByUsername($username);
    if (empty($user->getUid()) || $user->getActive() == 0) {
      throw new Core\ApiException('invalid username or password', 4, -1, 401);
    }

    // set up salt if not defined
    if ($user->getSalt() == NULL) {
      $user->setSalt(Core\Hash::generateSalt());
    }

    // generate hash and compare to stored hash this prevents refreshing token with a fake password.
    $hash = Core\Hash::generateHash($password, $user->getSalt());
    if ($user->getHash() != null && $user->getHash() != $hash) {
      throw new Core\ApiException('invalid username or password', 4, -1, 401);
    }

    // if token exists and is active, return it
    if (!empty($user->getToken())
      && !empty($user->getTokenTtl())
      && Core\Utilities::date_mysql2php($user->getTokenTtl()) > time()) {
      $user->setTokenTtl(Core\Utilities::date_php2mysql(strtotime(Config::$tokenLife)));
      return array('token' => $user->getToken());
    }

    //perform login
    $user->setHash($hash);
    $token = md5(time() . $username);
    $user->setToken($token);
    $user->setTokenTtl(Core\Utilities::date_php2mysql(strtotime(Config::$tokenLife)));
    $userMapper->save($user);

    return array('token' => $token);
  }
}
