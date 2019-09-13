<?php

namespace Gaterdata\Db;

/**
 * Class Manager.
 *
 * @package Gaterdata\Db
 */
class Manager {

  protected $mid;
  protected $accid;
  protected $uid;

  /**
   * Manager constructor.
   *
   * @param int $mid
   *   Manager ID.
   * @param int $accid
   *   Account ID.
   * @param string $uid
   *   User ID.
   */
  public function __construct($mid = NULL, $accid = NULL, $uid = NULL) {
    $this->mid = $mid;
    $this->accid = $accid;
    $this->uid = $uid;
  }

  /**
   * Get the manager ID.
   *
   * @return int
   *   Manager ID.
   */
  public function getMid() {
    return $this->mid;
  }

  /**
   * Set the manager ID.
   *
   * @param int $mid
   *   Manager ID.
   */
  public function setMid($mid) {
    $this->mid = $mid;
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
   * Return the values as an associative array.
   *
   * @return array
   *   Manager.
   */
  public function dump() {
    return [
      'mid' => $this->mid,
      'accid' => $this->accid,
      'uid' => $this->uid,
    ];
  }

}
