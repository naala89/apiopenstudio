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
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE user_account_role SET (uaid, rid, appid) WHERE uarid = ?';
      $bindParams = array(
        $userAccountRole->getUaid(),
        $userAccountRole->getRid(),
        $userAccountRole->getAppId(),
        $userAccountRole->getUarid(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
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
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find a user account role by its ID.
   *
   * @param int $uarid
   *   User account role ID.
   *
   * @return \Datagator\Db\UserAccountRole
   *   Mapped UserAccountRole object.
   */
  public function findByUarid($uarid) {
    $sql = 'SELECT * FROM user_account_role WHERE uarid = ?';
    $bindParams = array($uarid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
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
