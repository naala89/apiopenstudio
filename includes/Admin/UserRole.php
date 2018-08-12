<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Config;

Config::load();

class UserRole
{
  public function __construct()
  {
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
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
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