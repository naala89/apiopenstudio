<?php

/**
 * Container for data for an account row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Account
{
  protected $accId;
  protected $name;

  /**
   * @param null $accId
   * @param null $name
   */
  public function __construct($accId=NULL, $name=NULL)
  {
    $this->accId = $accId;
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
    return array(
      'accid' => $this->accId,
      'name' => $this->name,
    );
  }
}
