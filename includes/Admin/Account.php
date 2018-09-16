<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Core\ApiException;

/**
 * Class Account.
 *
 * @package Datagator\Admin
 */
class Account {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Datagator\Db\Account
   */
  private $account;

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
   * Create an account.
   *
   * @param string $name
   *   Account name.
   *
   * @return bool|array
   *   FALSE or the account.
   */
  public function create($name = NULL) {
    $account = new Db\Account(
      NULL,
      $name
    );
    $accountMapper = new Db\AccountMapper($this->db);

    try {
      $accountMapper->save($account);
      $this->account = $accountMapper->findByName($name);
    } catch (ApiException $e) {
      return FALSE;
    }

    return empty($this->account->getAccid()) ? FALSE : $this->account->dump();
  }

  /**
   * Get the account.
   *
   * @return array
   *   Account.
   */
  public function getAccount() {
    return $this->account->dump();
  }

  /**
   * Find an account by its account ID.
   *
   * @param int $accId
   *   Account ID.
   *
   * @return array
   *   Account.
   */
  public function findByAccountId($accId) {
    $accountMapper = new Db\AccountMapper($this->db);
    $this->account = $accountMapper->findByAccId($accId);
    return $this->account->dump();
  }

  /**
   * Find an account by it a user's uaid.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return array
   *   Account.
   */
  public function findByUaid($uaid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->findByAccountId($userAccount->getAccId());
  }

  /**
   * Add a user as owner.
   *
   * @param int $uid
   *   User ID.
   *
   * @return bool
   *   Success.
   */
  public function addOwner($uid) {
    $accountOwner = new Db\AccountOwner(
      NULL,
      $this->account->getAccid(),
      $uid
    );
    try {
      $accountOwnerMapper = new Db\AccountOwnerMapper($this->db);
      return $accountOwnerMapper->save($accountOwner);
    } catch (ApiException $e) {
      return FALSE;
    }
  }

}
