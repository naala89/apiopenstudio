<?php

namespace Datagator\Db;

/**
 * Class UserAccount.
 *
 * @package Datagator\Db
 */
class UserAccount {

  protected $uaid;
  protected $uid;
  protected $accid;

  /**
   * UserRole constructor.
   *
   * @param int $uaid
   *   The user account ID.
   * @param int $uid
   *   The user ID.
   * @param int $accid
   *   The account ID.
   */
  public function __construct($uaid = NULL, $uid = NULL, $accid = NULL) {
    $this->uaid = $uaid;
    $this->uid = $uid;
    $this->accid = $accid;
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
   * Get the account ID.
   *
   * @return int
   *   The account ID.
   */
  public function getAccId() {
    return $this->accid;
  }

  /**
   * Set the account ID.
   *
   * @param int $accid
   *   The account ID.
   */
  public function setAccId($accid) {
    $this->accid = $accid;
  }

  /**
   * Return the user account as an associative array.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return array(
      'uaid' => $this->uaid,
      'uid' => $this->uid,
      'accid' => $this->accid,
    );
  }

}
