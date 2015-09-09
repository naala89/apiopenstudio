<?php

/**
 * Container for data for an account row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Account
{
  protected $accId;
  protected $uid;
  protected $name;

  /**
   * @param null $accId
   * @param null $uid
   * @param null $name
   */
  public function __construct($accId=NULL, $uid=NULL, $name=NULL)
  {
    $this->accId = $accId;
    $this->uid = $uid;
    $this->name = $name;
  }

  /**
   * @return int accid
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
    $this->accId = $accId;
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
   * @return int name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->accId, 'accid');
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->name, 'name');
  }
}
