<?php

namespace Datagator\Db;

/**
 * Class ApplicationUser.
 *
 * @package Datagator\Db
 */
class ApplicationUser {

  protected $auid;
  protected $appid;
  protected $uid;

  /**
   * Account constructor.
   *
   * @param int $auid
   *   Application user ID.
   * @param int $appid
   *   Application ID.
   * @param string $uid
   *   User ID.
   */
  public function __construct($auid = NULL, $appid = NULL, $uid = NULL) {
    $this->auid = $auid;
    $this->appid = $appid;
    $this->uid = $uid;
  }

  /**
   * Get the application user ID.
   *
   * @return int
   *   Application user ID.
   */
  public function getAuid() {
    return $this->auid;
  }

  /**
   * Set the application user ID.
   *
   * @param int $auid
   *   Application user ID.
   */
  public function setAuid($auid) {
    $this->auid = $auid;
  }

  /**
   * Get the application ID.
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
   * Get the user ID.
   *
   * @return int
   *   User ID.
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * Set the user ID.
   *
   * @param string $uid
   *   User ID.
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Account.
   */
  public function dump() {
    return [
      'auid' => $this->auid,
      'appid' => $this->appid,
      'uid' => $this->uid,
    ];
  }

}
