<?php

namespace Datagator\Admin;

use Datagator\Db;

/**
 * Class UserAccount.
 *
 * @package Datagator\Admin
 */
class UserAccount {

  private $dbSettings;
  private $db;

  /**
   * UserAccount constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (count($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($this->dbSettings['options']) > 0 ? '?' . implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Create a user account.
   *
   * @param int $uid
   *   User ID.
   * @param int $accid
   *   Account ID.
   *
   * @return bool|int
   *   FALSE or user account ID.
   */
  public function create($uid, $accid) {
    $userAccount = new Db\UserAccount(
      NULL,
      $uid,
      $accid
    );

    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $result = $userAccountMapper->save($userAccount);
    if (!$result) {
      return FALSE;
    }

    $userAccount = $userAccountMapper->findByUidAccId($uid, $accid);
    if (!($uaid = $userAccount->getUaid())) {
      return FALSE;
    }

    return $uaid;
  }

  /**
   * Find all user accounts by the user ID.
   *
   * @param $uid
   *   User ID.
   *
   * @return array
   *   Array of UserAccount objects.
   */
  public function findByUserId($uid) {
    $userAccounts = [];

    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $results = $userAccountMapper->findByUid($uid);
    foreach ($results as $result) {
      $userAccounts[] = $result->dump();
    }

    return $userAccounts;
  }

  /**
   * Find all user accounts by the account ID.
   *
   * @param $accId
   *   Account ID.
   *
   * @return array
   *   Array of UserAccount objects.
   */
  public function findByAccId($accId) {
    $userAccounts = [];

    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $results = $userAccountMapper->findByAccId($accId);
    foreach ($results as $result) {
      $userAccounts[] = $result->dump();
    }

    return $userAccounts;
  }

  /**
   * Find user accounts by User ID and Account ID.
   *
   * @param int $uid
   *   User ID.
   * @param $accId
   *   Account ID.
   *
   * @return array
   *   User accounts array.
   */
  public function findByUidAccId($uid, $accId) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    return $userAccountMapper->findByUidAccId($uid, $accId);
  }

}
