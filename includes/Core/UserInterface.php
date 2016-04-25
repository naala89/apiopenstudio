<?php

/**
 *
 */

namespace Datagator\Core;
use Datagator\Config;
use Datagator\Db;

class UserInterface
{
  private $userMapper;
  private $roleMapper;
  private $applicationMapper;
  private $user;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->userMapper = new Db\UserMapper($dbLayer);
    $this->roleMapper = new Db\RoleMapper($dbLayer);
    $this->applicationMapper = new Db\ApplicationMapper($dbLayer);
    $this->user = new Db\User();
  }

  /**
   * @return \Datagator\Db\User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param $user
   * @return mixed
   */
  public function setUser($user)
  {
    $this->user = $user;
  }

  /**
   * @param null $user
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save($user=NULL)
  {
    if (empty($user)) {
      $user = $this->user;
    }
    return $this->userMapper->save($user);
  }

  /**
   * Login by username and password, and get a new token or the current active token.
   *
   * @param $username
   * @param $password
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function loginByUserPass($username, $password)
  {
    // validate username and active status
    $this->user = $this->userMapper->findByUsername($username);
    if (empty($this->user->getUid()) || $this->user->getActive() == 0) {
      throw new ApiException('invalid username or password', 4, -1, 401);
    }

    // if token exists and is active, return it
    if (!empty($this->user->getToken())
      && !empty($this->user->getTokenTtl())
      && Utilities::date_mysql2php($this->user->getTokenTtl()) > time()) {
      return $this->user->getToken();
    }

    // set up salt if not defined
    if ($this->user->getSalt() == NULL) {
      $this->user->setSalt(Hash::generateSalt());
    }

    // generate hash and compare to stored hash this prevents refreshing token with a fake password.
    $hash = Hash::generateHash($password, $this->user->getSalt());
    if ($this->user->getHash() != null && $this->user->getHash() != $hash) {
      throw new ApiException('invalid username or password', 4, -1, 401);
    }

    //perform login
    $this->user->setHash($hash);
    $token = md5(time() . $username);
    $this->user->setToken($token);
    $this->user->setTokenTtl(Utilities::date_php2mysql(strtotime(Config::$tokenLife)));
    $this->userMapper->save($this->user);

    return $token;
  }

  /**
   * validate a token.
   *
   * @param $token
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function validateToken($token)
  {
    $user = $this->userMapper->findBytoken($token);
    if (empty($user->getUid()) || $user->getActive() == 0) {
      throw new ApiException('permission denied', 4, -1, 401);
    }
    $this->user = $user;
    return true;
  }

  /**
   * Check to see if the user has a specific role with the application
   * $app can be application name or appid
   * $role can be role name or rid
   *
   * @param $app
   * @param $role
   * @return bool
   */
  public function hasRole($app, $role)
  {
    // convert app name to appid
    if (!filter_var($app, FILTER_VALIDATE_INT)) {
      $application = $this->applicationMapper->findByName($app);
      $app = $application->getAppId();
    }
    // convert role name to rid
    if (!filter_var($role, FILTER_VALIDATE_INT)) {
      $row = $this->roleMapper->findByName($role);
      $role = $row->getRid();
    }
    return $this->userMapper->hasRole($this->user->getUid(), $app, $role);
  }
}
