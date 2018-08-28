<?php

namespace Datagator\Db;

/**
 * Class UserAccount.
 *
 * @package Datagator\Db
 */
class UserAccount {

  protected $id;
  protected $uid;
  protected $accId;

  /**
   * UserAccount constructor.
   *
   * @param int $uaid
   *   The user account ID.
   * @param int $uid
   *   The user ID.
   * @param int $accId
   *   The account ID.
   */
  public function __construct($uaid = NULL, $uid = NULL, $accId = NULL) {
    $this->uaid = $uaid;
    $this->uid = $uid;
    $this->accId = $accId;
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
   * Return the values of this user account.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return array(
      'urid' => $this->uaid,
      'uid' => $this->uid,
      'accId' => $this->accId,
    );
  }

}
