<?php

namespace Datagator\Db;

/**
 * Class Administrator.
 *
 * @package Datagator\Db
 */
class Administrator {

  protected $aid;
  protected $uid;

  /**
   * Administrator constructor.
   *
   * @param int $aid
   *   Administrator ID.
   * @param int $uid
   *   User ID.
   */
  public function __construct($aid = NULL, $uid = NULL) {
    $this->aid = $aid;
    $this->uid = $uid;
  }

  /**
   * Get the administrator ID.
   *
   * @return int
   *   Administrator ID.
   */
  public function getAid() {
    return $this->aid;
  }

  /**
   * Set the administrator ID.
   *
   * @param int $aid
   *   Administrator ID.
   */
  public function setAid($aid) {
    $this->aid = $aid;
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
   *   Administrator.
   */
  public function dump() {
    return [
      'aid' => $this->aid,
      'uid' => $this->uid,
    ];
  }

}
