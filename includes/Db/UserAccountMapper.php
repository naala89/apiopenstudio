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
   * Save the UserAccount.
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
      $bindParams = array(
        $userAccount->getUid(),
        $userAccount->getAccId(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE user_account SET uid=?, accid=? WHERE uaid = ?';
      $bindParams = array(
        $userAccount->getUid(),
        $userAccount->getAccId(),
        $userAccount->getUaid(),
      );
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
   *   User role ID.
   *
   * @return \Datagator\Db\UserAccount
   *   Mapped UserAccount object.
   */
  public function findByUaid($uaid) {
    $sql = 'SELECT * FROM user_account WHERE uaid = ?';
    $bindParams = array($uaid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find all user accounts for a user.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserAccount objects.
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM user_account WHERE uid = ?';
    $bindParams = array($uid);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = [];
    while ($row = $recordSet->fetchRow()) {
      $entries[] = $this->mapArray($row);
    }

    return $entries;
  }

  /**
   * Find all user accounts for an account.
   *
   * @param int $accId
   *   Account ID.
   *
   * @return array
   *   Array of mapped UserAccount objects.
   */
  public function findByAccId($accId) {
    $sql = 'SELECT * FROM user_account WHERE accid = ?';
    $bindParams = array($accId);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Find a user account by user ID & account ID.
   *
   * @param int $uid
   *   User ID.
   * @param int $accId
   *   Account ID.
   *
   * @return array
   *   Array of mapped of UserAccount objects.
   */
  public function findByUidAccId($uid, $accId) {
    $sql = 'SELECT * FROM user_account WHERE uid = ? AND accid = ?';
    $bindParams = array($uid, $accId);
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
    $userAccount->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);

    return $userAccount;
  }

}
