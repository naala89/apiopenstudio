<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class UserAccountRoleMapper.
 *
 * @package Datagator\Db
 */
class UserAccountRoleMapper {

  protected $db;

  /**
   * UserAccountRoleMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save the UserAccount.
   *
   * @param \Datagator\Db\UserAccountRole $userAccountRole
   *   UserAccountRole object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(UserAccountRole $userAccountRole) {
    if ($userAccountRole->getUarid() == NULL) {
      $sql = 'INSERT INTO user_account_role (uaid, rid, appid) VALUES (?, ?, ?)';
      $bindParams = array(
        $userAccountRole->getUaid(),
        $userAccountRole->getRid(),
        $userAccountRole->getAppId(),
      );
    }
    else {
      $sql = 'UPDATE user_account_role SET (uaid, rid, appid) WHERE uarid = ?';
      $bindParams = array(
        $userAccountRole->getUaid(),
        $userAccountRole->getRid(),
        $userAccountRole->getAppId(),
        $userAccountRole->getUarid(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    throw new ApiException($this->db->ErrorMsg());
  }

  /**
   * Delete a user account role.
   *
   * @param \Datagator\Db\UserAccountRole $userAccountRole
   *   UserAccountRole object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(UserAccountRole $userAccountRole) {
    $sql = 'DELETE FROM user_account_role WHERE uarid = ?';
    $bindParams = array($userAccountRole->getUarid());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    throw new ApiException($this->db->ErrorMsg());
  }

  /**
   * Find a user account role by its ID.
   *
   * @param int $uarid
   *   User account role ID.
   *
   * @return \Datagator\Db\UserAccountRole
   *   Mapped UserAccountRole object.
   *
   * @throws ApiException
   */
  public function findByUarid($uarid) {
    $sql = 'SELECT * FROM user_account_role WHERE uarid = ?';
    $bindParams = array($uarid);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      throw new ApiException($this->db->ErrorMsg());
    }
    return $this->mapArray($row);
  }

  /**
   * Find a user account roles by user account ID.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return array
   *   Array of mapped UserAccountRole objects.
   *
   * @throws ApiException
   */
  public function findByUaid($uaid) {
    $sql = 'SELECT * FROM user_account_role WHERE uaid = ?';
    $bindParams = array($uaid);

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      throw new ApiException($this->db->ErrorMsg());
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Map a DB row to a UserAccountRole object.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Datagator\Db\UserAccountRole
   *   UserAccount object.
   */
  protected function mapArray(array $row) {
    $userAccountRole = new UserAccountRole();

    $userAccountRole->setUarid(!empty($row['uarid']) ? $row['uarid'] : NULL);
    $userAccountRole->setUaid(!empty($row['uaid']) ? $row['uaid'] : NULL);
    $userAccountRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $userAccountRole->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);

    return $userAccountRole;
  }

}
