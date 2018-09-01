<?php

namespace Datagator\Db;

/**
 * Class Account.
 *
 * @package Datagator\Db
 */
class Account {

  protected $accid;
  protected $name;

  /**
   * Account constructor.
   *
   * @param int $accid
   *   Account ID.
   * @param string $name
   *   Account name.
   */
  public function __construct($accid = NULL, $name = NULL) {
    $this->accid = $accid;
    $this->name = $name;
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
      'accid' => $this->accid,
      'name' => $this->name,
    );
  }

}
