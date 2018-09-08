<?php

namespace Datagator\Admin;

use Datagator\Core\ApiException;
use Datagator\Db;

/**
 * Class UserAccount
 *
 * @package Datagator\Admin
 */
class UserAccount {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Datagator\Db\UserAccount
   */
  private $userAccount;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   *
   * @throws ApiException
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptionsArr = [];
    foreach ($dbSettings['options'] as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('Failed to connect to the database.');
    }
  }

  /**
   * Get the stored user account.
   * @return mixed
   */
  public function getUserAccount() {
    return $this->userAccount->dump();
  }

  /**
   * Find a user account by user account ID.
   *
   * @param int $uaid
   *   user account ID.
   *
   * @return array|bool
   *   User account or FALSE on exception.
   */
  public function findByUaid($uaid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $this->userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->getUserAccount();
  }

  /**
   * Find a user account by user ID and account ID.
   *
   * @param int $uid
   *   user ID.
   * @param int $accId
   *   Account ID.
   *
   * @return array|bool
   *   User account or FALSE on exception.
   */
  public function findByUidAccId($uid, $accId) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $this->userAccount = $userAccountMapper->findByUidAccId($uid, $accId);
    return $this->userAccount->dump();
  }

}
