<?php

/**
 * Fetch and save role data.
 */

namespace Datagator\Db;
use Datagator\Core;

class RoleMapper
{
  protected $db;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\Role $role
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Role $role)
  {
    if ($role->getRid() == NULL) {
      $sql = 'INSERT INTO role (`name`) VALUES (?, ?)';
      $bindParams = array(
        $role->getName()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE external_user SET `name` = ? WHERE `rid` = ?';
      $bindParams = array(
        $role->getName(),
        $role->getRid()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg());
    }
    return TRUE;
  }

  /**
   * @param $rid
   * @return \Datagator\Db\ExternalUser
   */
  public function findByRid($rid)
  {
    $sql = 'SELECT * FROM external_user WHERE `rid` = ?';
    $bindParams = array($rid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $name
   * @return \Datagator\Db\ExternalUser
   */
  public function findByName($name)
  {
    $sql = 'SELECT * FROM external_user WHERE `name` = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param array $row
   * @return \Datagator\Db\Role
   */
  protected function mapArray(array $row)
  {
    $role = new Role();

    $role->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $role->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $role;
  }
}
