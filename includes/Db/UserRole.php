<?php

/**
 * Container for data for an user_role row.
 */

namespace Datagator\Db;

class UserRole
{
  protected $id;
  protected $uid;
  protected $rid;
  protected $appId;
  protected $accId;

  /**
   * @param null $urid
   * @param null $uid
   * @param null $rid
   * @param null $appId
   * @param null $accId
   */
  public function __construct($urid=NULL, $uid=NULL, $rid=NULL, $appId=NULL, $accId=NULL)
  {
    $this->urid = $urid;
    $this->uid = $uid;
    $this->rid = $rid;
    $this->appId = $appId;
    $this->accId = $accId;
  }

  /**
   * @return int utid
   */
  public function getUrid()
  {
    return $this->urid;
  }

  /**
   * @param $urid
   */
  public function setId($urid)
  {
    $this->urid = $urid;
  }

  /**
   * @return int uid
   */
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @param $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }

  /**
   * @return int rid
   */
  public function getRid()
  {
    return $this->rid;
  }

  /**
   * @param $rid
   */
  public function setRid($rid)
  {
    $this->rid = $rid;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appId;
  }

  /**
   * @param $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }

  /**
   * @return int accId
   */
  public function getAccId()
  {
    return $this->accId;
  }

  /**
   * @param $accId
   */
  public function setAccId($accId)
  {
    $this->appId = $accId;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    return array(
      'urid' => $this->urid,
      'uid' => $this->uid,
      'rid' => $this->rid,
      'appid' => $this->appId,
      'accid' => $this->accId,
    );
  }
}
