<?php

namespace Datagator\Db;

/**
 * Class Account.
 *
 * @package Datagator\Db
 */
class Account {

  protected $accId;
  protected $name;

  /**
   * Account constructor.
   *
   * @param int $accId
   *   Account ID.
   * @param string $name
   *   Account name.
   */
  public function __construct($accId = NULL, $name = NULL) {
    $this->accId = $accId;
    $this->name = $name;
  }

  /**
   * Get the account ID.
   *
   * @return int
   *   Account ID.
   */
  public function getAccId() {
    return $this->accId;
  }

  /**
   * Set the account ID.
   *
   * @param int $accId
   *   Account ID.
   */
  public function setAccId($accId) {
    $this->accId = $accId;
  }

  /**
   * Get the account name.
   *
   * @return string
   *   Account name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the account name.
   *
   * @param string $name
   *   Account name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Account.
   */
  public function dump() {
    return array(
      'accid' => $this->accId,
      'name' => $this->name,
    );
  }

}
