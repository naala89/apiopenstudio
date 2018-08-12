<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Config;
use GuzzleHttp;

Config::load();

class Account
{
  public function __construct()
  {}

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

    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $accountMapper = new Db\AccountMapper($db);
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