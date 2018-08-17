<?php

namespace Datagator\Admin;

use Datagator\Db;

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

  public function findByRid($rid) {
    $roles = [];
    $roleMapper = new Db\RoleMapper($this->db);
    $role = $roleMapper->findByRid($rid);
    return $role->dump();
  }

}
