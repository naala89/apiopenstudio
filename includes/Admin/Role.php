<?php

namespace Gaterdata\Admin;

use Gaterdata\Db\RoleMapper;
use Gaterdata\Core\ApiException;
use Gaterdata\Db\UserAccountRoleMapper;

/**
 * Class UserRole.
 *
 * @package Gaterdata\Admin
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
   * @var \Gaterdata\Db\Role
   */
  private $role;

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
   * Get the stored role.
   *
   * @return array
   *   Role.
   */
  public function getRole() {
    return $this->role->dump();
  }

  /**
   * Set the stored role.
   *
   * @param array $role
   *   Role.
   *
   * @return array
   *   Role.
   */
  public function setRole(array $role) {
    $object = new \Gaterdata\Db\Role(
      $role['rid'],
      $role['name']
    );
    $this->$role = $object;
    return $this->role->dump();
  }

  /**
   * Find all Roles.
   *
   * @return array
   *   Array of roles.
   */
  public function findAll() {
    $roleMapper = new RoleMapper($this->db);
    $results = $roleMapper->findAll();

    $roles = [];
    foreach ($results as $result) {
      $roles[] = $result->dump();
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
    $this->role = $roleMapper->findByRid($rid);
    return $this->getRole();
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
    $this->role = $roleMapper->findByName($name);
    return $this->getRole();
  }

}
