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
class UserAccountMapper extends Mapper {

  /**
   * UserAccountMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
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
    return $this->saveDelete($sql, $bindParams);
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
    $bindParams = [$userAccount->getUaid()];
    return $this->saveDelete($sql, $bindParams);
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
    $bindParams = [$uaid];
    return $this->fetchRow($sql, $bindParams);
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
    $bindParams = [$uid, $accid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find all user accounts for an Account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return array
   *   Array of mapped UserAccount objects.
   *
   * @throws ApiException
   */
  public function findByAccId($accid) {
    $sql = 'SELECT * FROM user_account WHERE accid = ?';
    $bindParams = [$accid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find all user accounts for a user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserAccount objects.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM user_account WHERE uid = ?';
    $bindParams = [$uid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row into a UserAccount object.
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
