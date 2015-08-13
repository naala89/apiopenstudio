<?php

/**
 * Container for data for an application row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Application
{
  protected $appId;
  protected $accId;
  protected $name;

  /**
   * @param null $appId
   * @param null $accId
   * @param null $name
   */
  public function __construct($appId=NULL, $accId=NULL, $name=NULL)
  {
    $this->appId = $appId;
    $this->accId = $accId;
    $this->name = $name;
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
    Core\Debug::variable($this->appId, 'appid');
    Core\Debug::variable($this->accId, 'accid');
    Core\Debug::variable($this->name, 'name');
  }
}
