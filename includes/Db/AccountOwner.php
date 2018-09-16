<?php

namespace Datagator\Db;

/**
 * Class AccountOwner.
 *
 * @package Datagator\Db
 */
class AccountOwner {

  protected $aoid;
  protected $accid;
  protected $uid;

  /**
   * Account constructor.
   *
   * @param int $aoid
   *   Account owner ID.
   * @param int $accid
   *   Account ID.
   * @param string $uid
   *   User ID.
   */
  public function __construct($aoid = NULL, $accid = NULL, $uid = NULL) {
    $this->aoid = $aoid;
    $this->accid = $accid;
    $this->uid = $uid;
  }

  /**
   * Get the account owber ID.
   *
   * @return int
   *   Account owner ID.
   */
  public function getAoid() {
    return $this->aoid;
  }

  /**
   * Set the account owner ID.
   *
   * @param int $aoid
   *   Account owner ID.
   */
  public function setAoid($aoid) {
    $this->aoid = $aoid;
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
    return array(
      'aoid' => $this->aoid,
      'accid' => $this->accid,
      'uid' => $this->uid,
    );
  }

}
