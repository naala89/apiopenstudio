<?php

namespace Gaterdata\Db;

/**
 * Class Vars.
 *
 * @package Gaterdata\Db
 */
class Vars {
  protected $id;
  protected $appId;
  protected $name;
  protected $val;

  /**
   * Vars constructor.
   *
   * @param int $id
   *   The var ID.
   * @param int $appId
   *   The var application ID.
   * @param string $name
   *   The var name.
   * @param mixed $val
   *   The var value.
   */
  public function __construct($id = NULL, $appId = NULL, $name = NULL, $val = NULL) {
    $this->id = $id;
    $this->accId = $appId;
    $this->name = $name;
    $this->val = $val;
  }

  /**
   * Get the var ID.
   *
   * @return int
   *   The var ID.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set the var ID.
   *
   * @param int $id
   *   The var ID.
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get the var application ID.
   *
   * @return int
   *   The var application ID.
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set the var application ID.
   *
   * @param int $appId
   *   The avr application ID.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get the var name.
   *
   * @return string
   *   The var name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the var name.
   *
   * @param string $name
   *   The var name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get the var value.
   *
   * @return string
   *   The var alue.
   */
  public function getVal() {
    return $this->val;
  }

  /**
   * Set the var value.
   *
   * @param mixed $val
   *   The var value.
   */
  public function setVal($val) {
    $this->val = $val;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Associative array of var attributes.
   */
  public function dump() {
    return [
      'id' => $this->id,
      'appId' => $this->appId,
      'name' => $this->name,
      'val' => $this->val,
    ];
  }

}
