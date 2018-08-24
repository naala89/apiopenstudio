<?php

namespace Datagator\Db;

/**
 * Class Invite.
 *
 * @package Datagator\Db
 */
class Invite {

  protected $id;
  protected $email;
  protected $token;

  /**
   * Invite constructor.
   *
   * @param int $email
   *   Invite email.
   * @param string $token
   *   Invite token.
   */
  public function __construct($email = NULL, $token = NULL) {
    $this->email = $email;
    $this->token = $token;
  }

  /**
   * Get the invite ID.
   *
   * @return int
   *   Invite ID.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set the invite ID.
   *
   * @param int $id
   *   Invite ID.
   */
  public function setId($id) {
    $this->id = $id;
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
    return array(
      'id' => $this->id,
      'email' => $this->email,
      'token' => $this->token,
    );
  }

}
