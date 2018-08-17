<?php

namespace Datagator\Admin;

use Datagator\Db;

class Account
{
  private $dbSettings;
  private $db;

  /**
   * Account constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings)
  {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (sizeof($dbSettings['options']) > 0) {
      foreach ($dbSettings['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($dbSettings['options']) > 0 ? '?'.implode('&', $dbSettings['options']) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Create an account.
   *
   * @param null $name
   *
   * @return bool|int
   */
  public function create($name=NULL)
  {
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

  public function findByName($name) {

  }
}