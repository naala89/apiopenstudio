<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class UserRoleMapper.
 *
 * @package Datagator\Db
 */
class UserRoleMapper {

  protected $db;

  /**
   * UserRoleMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save the userRole.
   *
   * @param \Datagator\Db\UserRole $userRole
   *   UserRole object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(UserRole $userRole) {
    if ($userRole->getUrid() == NULL) {
      $sql = 'INSERT INTO user_role (uaid, rid, appid) VALUES (?, ?, ?)';
      $bindParams = [
        $userRole->getUaid(),
        $userRole->getRid(),
        $userRole->getAppId(),
      ];
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE user_role SET uaid=?, rid=?, appid=? WHERE urid = ?';
      $bindParams = [
        $userRole->getUaid(),
        $userRole->getRid(),
        $userRole->getAppId(),
        $userRole->getUrid(),
      ];
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Delete a user role.
   *
   * @param \Datagator\Db\UserRole $userRole
   *   UserRole object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(UserRole $userRole) {
    $sql = 'DELETE FROM user_role WHERE urid = ?';
    $bindParams = array($userRole->getUrid());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find a user role by its ID.
   *
   * @param int $urid
   *   User role ID.
   *
   * @return \Datagator\Db\UserRole
   *   Mapped UserRole object.
   */
  public function findByUrid($urid) {
    $sql = 'SELECT * FROM user_role WHERE urid = ?';
    $bindParams = array($urid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find a user role by its role ID.
   *
   * @param int $rid
   *   Role ID.
   *
   * @return \Datagator\Db\UserRole
   *   Mapped UserRole object
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM user_role WHERE rid = ?';
    $bindParams = array($rid);

    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find all user roles for an application.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   */
  public function findByAppId($appId) {
    $sql = 'SELECT * FROM user_role WHERE appid = ?';
    $bindParams = array($appId);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = [];
    while ($row = $recordSet->fetchRow()) {
      $entries[] = $this->mapArray($row);
    }

    return $entries;
  }

  /**
   * Map a DB row to the internal attributes.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Datagator\Db\UserRole
   *   UserRole object.
   */
  protected function mapArray(array $row) {
    $userRole = new UserRole();

    $userRole->setUrid(!empty($row['urid']) ? $row['urid'] : NULL);
    $userRole->setUaid(!empty($row['uaid']) ? $row['uaid'] : NULL);
    $userRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $userRole->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);

    return $userRole;
  }

}
