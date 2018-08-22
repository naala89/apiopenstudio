<?php

namespace Datagator\Db;

/**
 * Class Application.
 *
 * @package Datagator\Db
 */
class Application {

  protected $appId;
  protected $accId;
  protected $name;

  /**
   * Application constructor.
   *
   * @param int $appId
   *   Application ID.
   * @param int $accId
   *   Account ID.
   * @param string $name
   *   Application name.
   */
  public function __construct($appId = NULL, $accId = NULL, $name = NULL) {
    $this->appId = $appId;
    $this->accId = $accId;
    $this->name = $name;
  }

  /**
   * Get application IOD.
   *
   * @return int
   *   Application ID.
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set the application ID.
   *
   * @param int $appId
   *   Application ID.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get the account ID.
   *
   * @return int
   *   Account ID.
   */
  public function getAccId() {
    return $this->accId;
  }

  /**
   * Set the account ID.
   *
   * @param int $accId
   *   Account ID.
   */
  public function setAccId($accId) {
    $this->accId = $accId;
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
    return array(
      'appId' => $this->appId,
      'accId' => $this->accId,
      'name' => $this->name,
    );
  }

}
