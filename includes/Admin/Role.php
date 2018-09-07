<?php

namespace Datagator\Admin;

use Datagator\Db\RoleMapper;
use Datagator\Core\ApiException;

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
   * UserRole constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   *
   * @throws ApiException
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptionsArr = [];
    foreach ($dbSettings['options'] as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('Failed to connect to the database.');
    }
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
    try {
      $results = $roleMapper->findAll();
    } catch (ApiException $e) {
      return FALSE;
    }
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
    try {
      $role = $roleMapper->findByRid($rid);
    } catch (ApiException $e) {
      return FALSE;
    }
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
  public function findByName($name) {
    $roleMapper = new RoleMapper($this->db);
    try {
      $role = $roleMapper->findByName($name);
    } catch (ApiException $e) {
      return FALSE;
    }
    return $role->dump();
  }

}
