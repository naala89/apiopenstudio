<?php

namespace Datagator\Db;

/**
 * Class Invite.
 *
 * @package Datagator\Db
 */
class Invite {

  protected $iid;
  protected $accid;
  protected $email;
  protected $token;

  /**
   * Invite constructor.
   *
   * @param int $iid
   *   Invite ID.
   * @param int $accid
   *   Account ID.
   * @param string $email
   *   Invite email.
   * @param string $token
   *   Invite token.
   */
  public function __construct($iid = NULL, $accid = NULL, $email = NULL, $token = NULL) {
    $this->iid = $iid;
    $this->accid = $accid;
    $this->email = $email;
    $this->token = $token;
  }

  /**
   * Get the invite ID.
   *
   * @return int
   *   Invite ID.
   */
  public function getIid() {
    return $this->iid;
  }

  /**
   * Set the invite ID.
   *
   * @param int $iid
   *   Invite ID.
   */
  public function setIid($iid) {
    $this->iid = $iid;
  }

  /**
   * Get the account ID.
   *
   * @return int
   *   Account ID.
   */
  public function getAccId() {
    return $this->accid;
  }

  /**
   * Set the account ID.
   *
   * @param int $accid
   *   Account ID.
   */
  public function setAccId($accid) {
    $this->accid = $accid;
  }

  /**
   * Get the invite email.
   *
   * @return string
   *   Invite email.
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * Set the invite email.
   *
   * @param string $email
   *   Invite email.
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * Get the invite token.
   *
   * @return string
   *   Invite token.
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Set the invite token.
   *
   * @param string $token
   *   Invite token.
   */
  public function setToken($token) {
    $this->token = $token;
  }

  /**
   * Return the invite as an associative array.
   *
   * @return array
   *   Invite.
   */
  public function dump() {
    return [
      'iid' => $this->iid,
      'accid' => $this->accid,
      'email' => $this->email,
      'token' => $this->token,
    ];
  }

}
