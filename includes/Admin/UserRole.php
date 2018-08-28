<?php

namespace Datagator\Admin;

use Datagator\Db;

/**
 * Class UserRole.
 *
 * @package Datagator\Admin
 */
class UserRole {

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
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Create a user role.
   *
   * @param int $uaid
   *   User account ID.
   * @param mixed $role
   *   Role name (string) or ID (int).
   * @param int $appid
   *   Application ID.
   *
   * @return bool
   *   Success.
   */
  public function create($uaid, $role, $appid = NULL) {
    $rid = $role;
    if (is_string($role)) {
      $roleMapper = new Db\RoleMapper($this->db);
      $role = $roleMapper->findByName($role);
      $rid = $role->getRid();
    }

    $userRole = new Db\UserRole(
      NULL,
      $uaid,
      $rid,
      $appid
    );

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $result = $userRoleMapper->save($userRole);
    if (!$result) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Find user roles by User ID and Account ID.
   *
   * @param int $uid
   *   User ID.
   * @param $accId
   *   Account ID.
   *
   * @return array
   *   Array of user roles.
   */
  public function findByUidAccId($uid, $accId) {
    $userRoles = [];

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $results = $userRoleMapper->findByUidAccId($uid, $accId);
    foreach ($results as $result) {
      $userRoles[] = $result->dump();
    }

    return $userRoles;
  }

  /**
   * Find all user roles by the account ID.
   *
   * @param $accId
   *   Account ID.
   *
   * @return array
   *   Array of UserRole objects.
   */
  public function findByAccId($accId) {
    $userRoles = [];

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $results = $userRoleMapper->findByAccId($accId);
    foreach ($results as $result) {
      $userRoles[] = $result->dump();
    }

    return $userRoles;
  }

  /**
   * Find all user roles by the application ID.
   *
   * @param $appId
   *   Application ID.
   *
   * @return array
   *   Array of UserRole objects.
   */
  public function findByAppId($appId) {
    $userRoles = [];

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $results = $userRoleMapper->findByAppId($appId);
    foreach ($results as $result) {
      $userRoles[] = $result->dump();
    }

    return $userRoles;
  }

}
