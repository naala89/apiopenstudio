<?php

namespace Gaterdata\Db;

/**
 * Class UserRole.
 *
 * @package Gaterdata\Db
 */
class UserRole {

    protected $urid;
    protected $accid;
    protected $appid;
    protected $uid;
    protected $rid;

  /**
   * UserRole constructor.
   *
   * @param int $urid
   *   User role ID.
   * @param int $accid
   *   Account ID
   * @param int $appid
   *   Application ID
   * @param int $uid
   *   User ID
   * @param int $rid
   *   The role ID.
   */
    public function __construct($urid = null, $accid = null, $appid = null, $uid = null, $rid = null)
    {
        $this->urid = $urid;
        $this->accid = $accid;
        $this->appid = $appid;
        $this->uid = $uid;
        $this->rid = $rid;
    }

  /**
   * Get the user role ID.
   *
   * @return int
   *   user role ID.
   */
    public function getUrid()
    {
        return $this->urid;
    }

  /**
   * Set the user role ID.
   *
   * @param int $urid
   *   User role ID.
   */
    public function setUrid($urid)
    {
        $this->urid = $urid;
    }

  /**
   * Get the account ID.
   *
   * @return int
   *   Account ID.
   */
    public function getAccid()
    {
        return $this->accid;
    }

  /**
   * Get the account ID.
   *
   * @param int $accid
   *   Account ID.
   */
    public function setAccid($accid)
    {
        $this->accid = $accid;
    }

  /**
   * Get the application ID.
   *
   * @return int
   *   Application ID.
   */
    public function getAppid()
    {
        return $this->appid;
    }

  /**
   * Get the application ID.
   *
   * @param int $appid
   *   Application ID.
   */
    public function setAppid($appid)
    {
        $this->appid = $appid;
    }

  /**
   * Get the user ID.
   *
   * @return int
   *   User ID.
   */
    public function getUid()
    {
        return $this->uid;
    }

  /**
   * Get the application user ID.
   *
   * @param int $uid
   *   Application user ID.
   */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

  /**
   * Get the role ID.
   *
   * @return int
   *   The role ID.
   */
    public function getRid()
    {
        return $this->rid;
    }

  /**
   * Set the role ID.
   *
   * @param int $rid
   *   The role ID.
   */
    public function setRid($rid)
    {
        $this->rid = $rid;
    }

  /**
   * Return the user account role as an associative array.
   *
   * @return array
   *   Associative array.
   */
    public function dump()
    {
        return [
        'urid' => $this->urid,
        'accid' => $this->accid,
        'appid' => $this->appid,
        'uid' => $this->uid,
        'rid' => $this->rid,
        ];
    }

}
