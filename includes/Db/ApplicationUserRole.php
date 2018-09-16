<?php

namespace Datagator\Db;

/**
 * Class ApplicationUserRole.
 *
 * @package Datagator\Db
 */
class ApplicationUserRole {

  protected $aurid;
  protected $auid;
  protected $rid;

  /**
   * UserAccountRole constructor.
   *
   * @param int $aurid
   *   Application user role ID.
   * @param int $auid
   *   Application user ID
   * @param int $rid
   *   The role ID.
   */
  public function __construct($aurid = NULL, $auid = NULL, $rid = NULL) {
    $this->aurid = $aurid;
    $this->auid = $auid;
    $this->rid = $rid;
  }

  /**
   * Get the application user role ID.
   *
   * @return int
   *   Application user role ID.
   */
  public function getAurid() {
    return $this->aurid;
  }

  /**
   * Set the application user role ID.
   *
   * @param int $aurid
   *   Application user role ID.
   */
  public function setAurid($aurid) {
    $this->aurid = $aurid;
  }

  /**
   * Get the application user ID.
   *
   * @return int
   *   Application user ID.
   */
  public function getAuid() {
    return $this->auid;
  }

  /**
   * Get the application user ID.
   *
   * @param int $auid
   *   Application user ID.
   */
  public function setAuid($auid) {
    $this->auid = $auid;
  }

  /**
   * Get the role ID.
   *
   * @return int
   *   The role ID.
   */
  public function getRid() {
    return $this->rid;
  }

  /**
   * Set the role ID.
   *
   * @param int $rid
   *   The role ID.
   */
  public function setRid($rid) {
    $this->rid = $rid;
  }

  /**
   * Return the user account role as an associative array.
   *
   * @return array
   *   Associative array.
   */
  public function dump() {
    return array(
      'aurid' => $this->aurid,
      'auid' => $this->auid,
      'rid' => $this->rid,
    );
  }

}
