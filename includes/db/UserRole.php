<?php

/**
 * Container for data for an user_role row.
 */

namespace Datagator\Db;
use Datagator\Core;

class UserRole
{
  protected $id;
  protected $uid;
  protected $rid;
  protected $appId;

  /**
   * @param null $id
   * @param null $uid
   * @param null $rid
   * @param null $appId
   */
  public function __construct($id=NULL, $uid=NULL, $rid=NULL, $appId=NULL)
  {
    $this->id = $id;
    $this->uid = $uid;
    $this->rid = $rid;
    $this->appId = $appId;
  }

  /**
   * @return int id
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param $id
   */
  public function setId($id)
  {
    $this->id = $id;
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
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->id, 'id');
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->rid, 'rid');
    Core\Debug::variable($this->appId, 'appid');
  }
}
