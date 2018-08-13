<?php

namespace Datagator\Admin;

use Datagator\Db;

class UserRole
{
  private $settings;

  /**
   * UserRole constructor.
   *
   * @param $settings
   */
  public function __construct($settings)
  {
    $this->settings = $settings;
  }

  /**
   * Create a user role.
   *
   * @param $uid
   * @param $roleName
   * @param null $appid
   * @param null $accid
   *
   * @return bool
   */
  public function create($uid, $roleName, $appid=NULL, $accid=NULL)
  {
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
    $db = \ADONewConnection($dsn);

    $roleMapper = new Db\RoleMapper($db);
    $role = $roleMapper->findByName($roleName);
    $rid = $role->getRid();

    $userRole = new Db\UserRole(
      NULL,
      $uid,
      $rid,
      $appid,
      $accid
    );

    $userRoleMapper = new Db\UserRoleMapper($db);
    $result = $userRoleMapper->save($userRole);
    if (!$result) {
      return FALSE;
    }

    return TRUE;
  }
}