<?php

namespace Datagator\Admin;

use Datagator\Db\RoleMapper;

/**
 * Class UserRole.
 *
 * @package Datagator\Admin
 */
class Role {
  private $dbSettings;
  private $db;

  /**
   * UserRole constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (count($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($this->dbSettings['options']) > 0 ? '?' . implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://' .
      $this->dbSettings['username'] . ':' .
      $this->dbSettings['password'] . '@' .
      $this->dbSettings['host'] . '/' .
      $this->dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Find all Roles.
   *
   * @return array
   *   Array of roles.
   */
  public function findAll() {
    $roles = [];
    $roleMapper = new RoleMapper($this->db);
    $results = $roleMapper->findAll();
    foreach ($results as $result) {
      $role = $result->dump();
      $roles[$role['rid']] = $role;
    }
    return $roles;
  }

  /**
   * Find a role by its ID.
   *
   * @param int $rid
   *   Role ID.
   *
   * @return array
   *   The role attributes.
   */
  public function findByRid($rid) {
    $roleMapper = new RoleMapper($this->db);
    $role = $roleMapper->findByRid($rid);
    return $role->dump();
  }

}
