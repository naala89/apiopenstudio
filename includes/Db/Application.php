<?php

namespace Datagator\Db;

/**
 * Class Application
 * Container for data for an application row.
 *
 * @package Datagator\Db
 */
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
   * @param $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
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
   * Return the values as an associative array.
   * @return array
   */
  public function dump()
  {
    return array(
      'appId' => $this->appId,
      'accId' => $this->accId,
      'name' => $this->name,
    );
  }

}
