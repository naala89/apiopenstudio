<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use Cascade\Cascade;

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
    }
    else {
      $sql = 'UPDATE user_account SET uid=?, accid=? WHERE uaid = ?';
      $bindParams = [
        $userAccount->getUid(),
        $userAccount->getAccId(),
        $userAccount->getUaid(),
      ];
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
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find a user account by its ID.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return \Datagator\Db\UserAccount
   *   Mapped UserAccount object.
   *
   * @throws ApiException
   */
  public function findByUaid($uaid) {
    $sql = 'SELECT * FROM user_account WHERE uaid = ?';
    $bindParams = array($uaid);
    $row = $this->db->GetRow($sql, $bindParams);
    if ($row === FALSE) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find a user account by user ID and Account ID.
   *
   * @param int $uid
   *   User ID.
   * @param int $accid
   *   Account ID.
   *
   * @return \Datagator\Db\UserAccount
   *   Mapped UserAccount object.
   *
   * @throws ApiException
   */
  public function findByUidAccId($uid, $accid) {
    $sql = 'SELECT * FROM user_account WHERE uid = ? AND accid = ?';
    $bindParams = array($uid, $accid);
    $row = $this->db->GetRow($sql, $bindParams);
    if ($row === FALSE) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find all user accounts for an Account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return \Datagator\Db\UserAccount
   *   Mapped UserAccount object.
   *
   * @throws ApiException
   */
  public function findByAccId($accid) {
    $sql = 'SELECT * FROM user_account WHERE AND accid = ?';
    $bindParams = array($accid);
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
