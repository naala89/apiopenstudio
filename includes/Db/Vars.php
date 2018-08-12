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
   * @param $id
   */
  public function setId($id)
  {
    $this->id = $id;
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
   * @return string name
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
    return array(
      'id' => $this->id,
      'appId' => $this->appId,
      'name' => $this->name,
      'val' => $this->val,
    );
  }
}
