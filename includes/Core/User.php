<?php

/**
 *
 */

namespace Datagator\Core;
use Datagator\Db;

class User
{
  protected $db;
  protected $user;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\User $user
   */
  public function setUser(Db\User $user)
  {
    $this->user = $user;
  }

  /**
   * @return \Datagator\Db\User|NULL
   */
  public function getUser()
  {
    return $this->user;
  }

  public function save()
  {
    $mapper = new Db\UserMapper($this->db);
    $mapper->save($this->user);
  }

  /**
   * @param $token
   * @return \Datagator\Db\User
   */
  public function findByToken($token)
  {
    $mapper = new Db\UserMapper($this->db);
    $this->user = $mapper->findBytoken($token);
    return $this->user;
  }

  /**
   * @param $username
   * @return \Datagator\Db\User
   */
  public function findByUsername($username)
  {
    $mapper = new Db\UserMapper($this->db);
    $this->user = $mapper->findByUsername($username);
    return $this->user;
  }

  /**
   * @param $appId
   * @param $roleName
   * @return bool
   */
  public function hasRole($appId, $roleName)
  {
    $uid = $this->user->getUid();
    if (empty($uid)) {
      return FALSE;
    }
    $mapper = new Db\UserMapper($this->db);
    return $mapper->hasRole($uid, $appId, $roleName);
  }
}
