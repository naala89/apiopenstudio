<?php

namespace Datagator\Db;

/**
 * Class UserRole.
 *
 * @package Datagator\Db
 */
class UserRole {

  protected $urid;
  protected $uaid;
  protected $rid;
  protected $appid;

  /**
   * UserRole constructor.
   *
   * @param int $urid
   *   The user role ID.
   * @param int $uaid
   *   The user account ID.
   * @param int $rid
   *   The role ID.
   * @param int $appid
   *   The application ID.
   */
  public function __construct($urid, $uaid, $rid, $appid) {
    $this->urid = $urid;
    $this->uaid = $uaid;
    $this->rid = $rid;
    $this->appid = $appid;
  }

  /**
   * Get the user role ID.
   *
   * @return int
   *   The user role ID.
   */
  public function getUrid() {
    return $this->urid;
  }

  /**
   * Set the user role ID.
   *
   * @param int $urid
   *   The user role ID.
   */
  public function setUrid($urid) {
    $this->urid = $urid;
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
   * @param int $appid
   *   The application ID.
   */
  public function setAppId($appid) {
    $this->appid = $appid;
  }

  /**
   * Return the values of this user role.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return array(
      'urid' => $this->urid,
      'uaid' => $this->uaid,
      'rid' => $this->rid,
      'appId' => $this->appId,
    );
  }

}
