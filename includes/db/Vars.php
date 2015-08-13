<?php

/**
 * Container for data for a vars row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Vars
{
  protected $id;
  protected $appId;
  protected $name;
  protected $val;

  /**
   * @param null $id
   * @param null $appId
   * @param null $name
   * @param null $val
   */
  public function __construct($id=NULL, $appId=NULL, $name=NULL, $val=NULL)
  {
    $this->id = $id;
    $this->accId = $appId;
    $this->name = $name;
    $this->val = $val;
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
   * @return string name
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
   * @return string val
   */
  public function getVal()
  {
    return $this->val;
  }

  /**
   * @param $val
   */
  public function setVal($val)
  {
    $this->val = $val;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->id, 'id');
    Core\Debug::variable($this->appId, 'appid');
    Core\Debug::variable($this->name, 'name');
    Core\Debug::variable($this->val, 'val');
  }
}
