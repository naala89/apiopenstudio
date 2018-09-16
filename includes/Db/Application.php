<?php

namespace Datagator\Db;

/**
 * Class Application.
 *
 * @package Datagator\Db
 */
class Application {

  protected $appid;
  protected $accid;
  protected $name;

  /**
   * Application constructor.
   *
   * @param int $appid
   *   Application ID.
   * @param int $accid
   *   Account ID.
   * @param string $name
   *   Application name.
   */
  public function __construct($appid = NULL, $accid = NULL, $name = NULL) {
    $this->appid = $appid;
    $this->accid = $accid;
    $this->name = $name;
  }

  /**
   * Get application IOD.
   *
   * @return int
   *   Application ID.
   */
  public function getAppid() {
    return $this->appid;
  }

  /**
   * Set the application ID.
   *
   * @param int $appid
   *   Application ID.
   */
  public function setAppid($appid) {
    $this->appid = $appid;
  }

  /**
   * Get the account ID.
   *
   * @return int
   *   Account ID.
   */
  public function getAccid() {
    return $this->accid;
  }

  /**
   * Set the account ID.
   *
   * @param int $accid
   *   Account ID.
   */
  public function setAccid($accid) {
    $this->accid = $accid;
  }

  /**
   * Get the application name.
   *
   * @return int
   *   Application name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the application name.
   *
   * @param string $name
   *   Application name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Application.
   */
  public function dump() {
    return [
      'appid' => $this->appid,
      'accid' => $this->accid,
      'name' => $this->name,
    ];
  }

}
