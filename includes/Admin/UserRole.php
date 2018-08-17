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
  public function __construct(array $dbSettings)
  {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (sizeof($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->dbSettings['options']) > 0 ? '?'.implode('&', $this->dbSettings['options']) : '';
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
   * @param int $uid
   *   User ID.
   * @param int $roleName
   *   Role name.
   * @param null $appid
   *   Application ID.
   * @param null $accid
   *   Account ID.
   *
   * @return bool
   *   Success.
   */
  public function create($uid, $roleName, $appid = NULL, $accid = NULL) {
    $roleMapper = new Db\RoleMapper($this->db);
    $role = $roleMapper->findByName($roleName);
    $rid = $role->getRid();

    $userRole = new Db\UserRole(
      NULL,
      $uid,
      $rid,
      $appid,
      $accid
    );

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $result = $userRoleMapper->save($userRole);
    if (!$result) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Find user roles by uid and accId.
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

}
