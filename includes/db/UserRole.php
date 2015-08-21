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
   * @param $val
   */
  public function setId($val)
  {
    $this->id = $val;
  }

  /**
   * @return int uid
   */
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @param $val
   */
  public function setUid($val)
  {
    $this->uid = $val;
  }

  /**
   * @return int rid
   */
  public function getRid()
  {
    return $this->rid;
  }

  /**
   * @param $val
   */
  public function setRid($val)
  {
    $this->rid = $val;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appId;
  }

  /**
   * @param $val
   */
  public function setAppId($val)
  {
    $this->appId = $val;
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
