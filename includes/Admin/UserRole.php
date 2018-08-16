<?php

namespace Datagator\Admin;

use Datagator\Db;

/**
 * Class UserRole.
 *
 * @package Datagator\Admin
 */
class UserRole {
  private $settings;
  private $db;

  /**
   * UserRole constructor.
   *
   * @param array $settings
   */
  public function __construct(array $settings)
  {
    $this->settings = $settings;

    $dsnOptions = '';
    if (sizeof($this->settings['db']['options']) > 0) {
      foreach ($this->settings['db']['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->settings['db']['options']) > 0 ? '?'.implode('&', $this->settings['db']['options']) : '';
    $dsn = $this->settings['db']['driver'] . '://'
      . $this->settings['db']['username'] . ':'
      . $this->settings['db']['password'] . '@'
      . $this->settings['db']['host'] . '/'
      . $this->settings['db']['database'] . $dsnOptions;
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

}
