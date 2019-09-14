<?php

namespace Gaterdata\Db;

/**
 * Class ApplicationUserRole.
 *
 * @package Gaterdata\Db
 */
class ApplicationUserRole {

  protected $aurid;
  protected $appid;
  protected $uid;
  protected $rid;

  /**
   * UserAccountRole constructor.
   *
   * @param int $aurid
   *   Application user role ID.
   * @param int $appid
   *   Application ID
   * @param int $uid
   *   User ID
   * @param int $rid
   *   The role ID.
   */
  public function __construct($aurid = NULL, $appid = NULL, $uid = NULL, $rid = NULL) {
    $this->aurid = $aurid;
    $this->appid = $appid;
    $this->uid = $uid;
    $this->rid = $rid;
  }

  /**
   * Get the application user role ID.
   *
   * @return int
   *   Application user role ID.
   */
  public function getAurid() {
    return $this->aurid;
  }

  /**
   * Set the application user role ID.
   *
   * @param int $aurid
   *   Application user role ID.
   */
  public function setAurid($aurid) {
    $this->aurid = $aurid;
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
   * Get the application ID.
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
   * Get the application user ID.
   *
   * @param int $uid
   *   Application user ID.
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * Get the role ID.
   *
   * @return int
   *   The role ID.
   */
  public function getRid() {
    return $this->rid;
  }

  /**
   * Set the role ID.
   *
   * @param int $rid
   *   The role ID.
   */
  public function setRid($rid) {
    $this->rid = $rid;
  }

  /**
   * Return the user account role as an associative array.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return [
      'aurid' => $this->aurid,
      'appid' => $this->appid,
      'uid' => $this->uid,
      'rid' => $this->rid,
    ];
  }

}
