<?php

namespace Gaterdata\Db;

/**
 * Class Role.
 *
 * @package Gaterdata\Db
 */
class Role {

  protected $rid;
  protected $name;

  /**
   * Role constructor.
   *
   * @param int $rid
   *   Role ID.
   * @param string $name
   *   Role name.
   */
  public function __construct($rid = NULL, $name = NULL) {
    $this->rid = $rid;
    $this->name = $name;
  }

  /**
   * Get the role ID.
   *
   * @return int
   *   Role ID.
   */
  public function getRid() {
    return $this->rid;
  }

  /**
   * Set the role ID.
   *
   * @param int $rid
   *   Role ID.
   */
  public function setRid($rid) {
    $this->rid = $rid;
  }

  /**
   * Get the role name.
   *
   * @return int
   *   Name
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the role name.
   *
   * @param string $name
   *   Role name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Role object.
   */
  public function dump() {
    return [
      'rid' => $this->rid,
      'name' => $this->name,
    ];
  }

}
