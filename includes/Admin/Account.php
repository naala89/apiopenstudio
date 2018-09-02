<?php

namespace Datagator\Admin;

use Datagator\Db;
use Monolog\Logger;

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
   * @var \Monolog\Logger
   */
  private $logger;
  /**
   * @var \Datagator\Db\Account
   */
  private $account;

  /**
   * Account constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   * @param \Monolog\Logger $logger
   *   Logger.
   */
  public function __construct(array $dbSettings, Logger $logger) {
    $this->dbSettings = $dbSettings;
    $this->logger = $logger;

    $dsnOptions = '';
    if (count($dbSettings['options']) > 0) {
      foreach ($dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($dbSettings['options']) > 0 ? '?' . implode('&', $dbSettings['options']) : '';
    $dsn = $dbSettings['driver'] . '://' .
      $dbSettings['username'] . ':' .
      $dbSettings['password'] . '@' .
      $dbSettings['host'] . '/' .
      $dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Create an account.
   *
   * @param string $name
   *   Account name.
   *
   * @return bool|int
   *   FALSE or the account ID.
   */
  public function create($name = NULL) {
    $account = new Db\Account(
      NULL,
      $name
    );
    $accountMapper = new Db\AccountMapper($this->db);
    $result = $accountMapper->save($account);
    if (!$result) {
      return FALSE;
    }

    $this->account = $accountMapper->findByName($name);
    if (!($this->account->getAccId())) {
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
    $this->account = $accountMapper->findByAccId($accId);
    return $this->account->dump();
  }

}
