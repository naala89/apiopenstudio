<?php

namespace Datagator\Db;

/**
 * Class UserRole.
 *
 * @package Datagator\Db
 */
class UserRole {

  protected $id;
  protected $uid;
  protected $rid;
  protected $appId;
  protected $accId;

  /**
   * UserRole constructor.
   *
   * @param int $urid
   *   The user role ID.
   * @param int $uid
   *   The user ID.
   * @param int $rid
   *   The role ID.
   * @param int $appId
   *   The application ID.
   * @param int $accId
   *   The account ID.
   */
  public function __construct($urid = NULL, $uid = NULL, $rid = NULL, $appId = NULL, $accId = NULL) {
    $this->urid = $urid;
    $this->uid = $uid;
    $this->rid = $rid;
    $this->appId = $appId;
    $this->accId = $accId;
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
  public function setId($urid) {
    $this->urid = $urid;
  }

  /**
   * Get the user ID.
   *
   * @return int
   *   The user ID.
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * Set the user ID.
   *
   * @param int $uid
   *   The user ID.
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
   * Get the application ID.
   *
   * @return int
   *   The application ID.
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set the application ID.
   *
   * @param int $appId
   *   The application ID.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get the account ID.
   *
   * @return int
   *   The account ID.
   */
  public function getAccId() {
    return $this->accId;
  }

  /**
   * Set the account ID.
   *
   * @param int $accId
   *   The account ID.
   */
  public function setAccId($accId) {
    $this->accId = $accId;
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
      'uid' => $this->uid,
      'rid' => $this->rid,
      'appId' => $this->appId,
      'accId' => $this->accId,
    );
  }

}
