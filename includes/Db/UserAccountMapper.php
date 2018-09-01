<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class UserAccountMapper.
 *
 * @package Datagator\Db
 */
class UserAccountMapper {

  protected $db;

  /**
   * UserAccountMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save the user account.
   *
   * @param \Datagator\Db\UserAccount $userAccount
   *   UserAccount object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(UserAccount $userAccount) {
    if ($userAccount->getUaid() == NULL) {
      $sql = 'INSERT INTO user_account (uid, accid) VALUES (?, ?)';
      $bindParams = [
        $userAccount->getUid(),
        $userAccount->getAccId(),
      ];
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE user_account SET uid=?, accid=? WHERE uaid = ?';
      $bindParams = [
        $userAccount->getUid(),
        $userAccount->getAccId(),
        $userAccount->getUaid(),
      ];
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Delete a user account.
   *
   * @param \Datagator\Db\UserAccount $userAccount
   *   UserAccount object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(UserAccount $userAccount) {
    $sql = 'DELETE FROM user_account WHERE uaid = ?';
    $bindParams = array($userAccount->getUaid());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find a user account by its ID.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return \Datagator\Db\UserAccount
   *   Mapped UserAccount object.
   */
  public function findByUrid($uaid) {
    $sql = 'SELECT * FROM user_account WHERE uaid = ?';
    $bindParams = array($uaid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Map a DB row to the internal attributes.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Datagator\Db\UserAccount
   *   UserAccount object.
   */
  protected function mapArray(array $row) {
    $userAccount = new UserAccount();

    $userAccount->setUaid(!empty($row['uaid']) ? $row['uaid'] : NULL);
    $userAccount->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $userAccount->setAppId(!empty($row['accid']) ? $row['accid'] : NULL);

    return $userAccount;
  }

}
