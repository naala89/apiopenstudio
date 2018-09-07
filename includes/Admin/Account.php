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
    } catch (ApiException $e) {
      return FALSE;
    }

    try {
      $this->account = $accountMapper->findByName($name);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($this->account->getAccId())) {
      return FALSE;
    }

    return $this->account->dump();
  }

  /**
   * Get the account.
   *
   * @return array
   *   associative array.
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
   *   Account attributes array.
   */
  public function findByAccountId($accId) {
    $accountMapper = new Db\AccountMapper($this->db);
    try {
      $this->account = $accountMapper->findByAccId($accId);
    } catch (ApiException $e) {
      return FALSE;
    }
    return $this->account->dump();
  }

}
