<?php

namespace Datagator\Admin;

use Datagator\Db;

/**
 * Class Account.
 *
 * @package Datagator\Admin
 */
class Account {

  private $dbSettings;
  private $db;

  /**
   * Account constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

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

    $account = $accountMapper->findByName($name);
    $accId = $account->getAccId();
    if (!$accId) {
      return FALSE;
    }

    return $accId;
  }

}
