<?php

namespace Datagator\Db;

/**
 * Class Sysadmin.
 *
 * @package Datagator\Db
 */
class Sysadmin {

  protected $sid;
  protected $uid;

  /**
   * SysadminMapper constructor.
   *
   * @param int $sid
   *   System admin ID.
   * @param int $uid
   *   User ID.
   */
  public function __construct($sid = NULL, $uid = NULL) {
    $this->sid = $sid;
    $this->uid = $uid;
  }

  /**
   * Get the system admin ID.
   *
   * @return int
   *   System admin ID.
   */
  public function getSid() {
    return $this->sid;
  }

  /**
   * Get the system admin ID.
   *
   * @param int $sid
   *   System admin ID.
   */
  public function setSid($sid) {
    $this->sid = $sid;
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
   * @param int $uid
   *   User ID.
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Sysadmin.
   */
  public function dump() {
    return [
      'sid' => $this->sid,
      'uid' => $this->uid,
    ];
  }

}
