<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use Cascade\Cascade;

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
    }
    else {
      $sql = 'UPDATE external_user SET name = ? WHERE rid = ?';
      $bindParams = array(
        $role->getName(),
        $role->getRid(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Delete a Role.
   *
   * @param \Datagator\Db\Role $role
   *   Role object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Role $role) {
    $sql = 'DELETE FROM role WHERE rid = ?';
    $bindParams = array($role->getRid());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find all roles.
   *
   * @return array
   *   Array of role objects.
   *
   * @throws ApiException
   */
  public function findAll() {
    $sql = 'SELECT * FROM role';

    $recordSet = $this->db->Execute($sql);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Find a role by its ID.
   *
   * @param int $rid
   *   role ID.
   *
   * @return \Datagator\Db\Role
   *   Role object.
   *
   * @throws ApiException
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM role WHERE rid = ?';
    $bindParams = array($rid);
    $row = $this->db->GetRow($sql, $bindParams);
    if ($row === FALSE) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
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
   *
   * @throws ApiException
   */
  public function findByName($name) {
    $sql = 'SELECT * FROM role WHERE name = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    if ($row === FALSE) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
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
