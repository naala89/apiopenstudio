<?php

namespace Datagator\Admin;

use Datagator\Db;

class Account
{
  private $settings;

  /**
   * Account constructor.
   *
   * @param $settings
   */
  public function __construct($settings)
  {
    $this->settings = $settings;
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
    $dsnOptions = '';
    if (sizeof($this->settings['db']['options']) > 0) {
      foreach ($this->settings['db']['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->settings['db']['options']) > 0 ? '?'.implode('&', $this->settings['db']['options']) : '';
    $dsn = $this->settings['db']['driver'] . '://'
      . $this->settings['db']['username'] . ':'
      . $this->settings['db']['password'] . '@'
      . $this->settings['db']['host'] . '/'
      . $this->settings['db']['database'] . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $account = new Db\Account(
      NULL,
      $name
    );
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