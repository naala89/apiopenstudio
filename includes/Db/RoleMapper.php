<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class RoleMapper.
 *
 * @package Datagator\Db
 */
class RoleMapper {

  protected $db;

  /**
   * RoleMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connector.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save a Role object into the DB.
   *
   * @param \Datagator\Db\Role $role
   *   Role object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Role $role) {
    if ($role->getRid() == NULL) {
      $sql = 'INSERT INTO role (name) VALUES (?, ?)';
      $bindParams = array(
        $role->getName(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE external_user SET name = ? WHERE rid = ?';
      $bindParams = array(
        $role->getName(),
        $role->getRid(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find a role by its ID.
   *
   * @param int $rid
   *   role ID.
   *
   * @return \Datagator\Db\Role
   *   Role object.
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM role WHERE rid = ?';
    $bindParams = array($rid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find a role by its name.
   *
   * @param string $name
   *   Role name.
   *
   * @return \Datagator\Db\Role
   *   Role object.
   */
  public function findByName($name) {
    $sql = 'SELECT * FROM role WHERE name = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Map a DB row to the internal attributes.
   *
   * @param array $row
   *   DB row.
   *
   * @return \Datagator\Db\Role
   *   Role object.
   */
  protected function mapArray(array $row) {
    $role = new Role();

    $role->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $role->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $role;
  }

}
