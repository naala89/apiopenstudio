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
   * @param $val
   */
  public function setAccId($val)
  {
    $this->accId = $val;
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
   * @return int name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param $val
   */
  public function setName($val)
  {
    $this->name = $val;
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
