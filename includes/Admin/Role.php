<?php

namespace Datagator\Admin;

use Datagator\Db\RoleMapper;
use Monolog\Logger;

/**
 * Class UserRole.
 *
 * @package Datagator\Admin
 */
class Role {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Monolog\Logger
   */
  private $logger;

  /**
   * UserRole constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   * @param \Monolog\Logger $logger
   *   Logger.
   */
  public function __construct(array $dbSettings, Logger $logger) {
    $this->dbSettings = $dbSettings;
    $this->logger = $logger;

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

  /**
   * Find a role by its name.
   *
   * @param string $name
   *   Role name.
   *
   * @return array
   *   The role attributes.
   */
  public function fincByName($name) {
    $roleMapper = new RoleMapper($this->db);
    $role = $roleMapper->findByName($name);
    return $role->dump();
  }

}
