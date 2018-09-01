<?php

namespace Datagator\Db;

/**
 * Class UserAccountRole.
 *
 * @package Datagator\Db
 */
class UserAccountRole {

  protected $uarid;
  protected $uaid;
  protected $rid;
  protected $appid;

  /**
   * UserAccountRole constructor.
   *
   * @param int $uarid
   *   The user account role ID.
   * @param int $uaid
   *   The user account ID.
   * @param int $rid
   *   The role ID.
   * @param int $appId
   *   The application ID.
   */
  public function __construct($uarid = NULL, $uaid = NULL, $rid = NULL, $appId = NULL) {
    $this->uarid = $uarid;
    $this->uaid = $uaid;
    $this->rid = $rid;
    $this->appid = $appId;
  }

  /**
   * Get the user account role ID.
   *
   * @return int
   *   The user account role ID.
   */
  public function getUarid() {
    return $this->uarid;
  }

  /**
   * Set the user account role ID.
   *
   * @param int $uarid
   *   The user account role ID.
   */
  public function setUarid($uarid) {
    $this->uarid = $uarid;
  }

  /**
   * Get the user account ID.
   *
   * @return int
   *   The user account ID.
   */
  public function getUaid() {
    return $this->uaid;
  }

  /**
   * Set the user account ID.
   *
   * @param int $uaid
   *   The user account ID.
   */
  public function setUaid($uaid) {
    $this->uaid = $uaid;
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
   * Get the application ID.
   *
   * @return int
   *   The application ID.
   */
  public function getAppId() {
    return $this->appid;
  }

  /**
   * Set the application ID.
   *
   * @param int $appId
   *   The application ID.
   */
  public function setAppId($appId) {
    $this->appid = $appId;
  }

  /**
   * Return the user account role as an associative array.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return array(
      'uarid' => $this->uarid,
      'uaid' => $this->uaid,
      'rid' => $this->rid,
      'appid' => $this->appid,
    );
  }

}
