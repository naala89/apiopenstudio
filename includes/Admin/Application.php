<?php

namespace Datagator\Admin;

use Datagator\Db;

class Application
{
  private $dbSettings;

  /**
   * Application constructor.
   * 
   * @param $dbSettings
   */
  public function __construct($dbSettings) {
    $this->dbSettings = $dbSettings;
  }

  /**
   * Get all an applications for an account.
   *
   * @param string $accId
   *   ID of the account.
   *
   * @return array
   */
  public function getByAccount($accId) {
    $dsnOptions = '';
    if (sizeof($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->dbSettings['options']) > 0 ? '?'.implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $applicationMapper = new Db\ApplicationMapper($db);
    $results = $applicationMapper->findByAccId($accId);
    $applications = [];
    foreach ($results as $result) {
      $applications[] = $result->debug();
    }

    return $applications;
  }

    /**
     * Create an application.
     *
     * @param string $name
     *   Name of the application.
     * @param string $accId
     *   ID of the account.
     *
     * @return bool|int
     */
    public function create($name, $accId) {
    $dsnOptions = '';
    if (sizeof($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->dbSettings['options']) > 0 ? '?'.implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $application = new Db\Application(
      NULL,
      $accId,
      $name
    );
    $applicationMapper = new Db\ApplicationMapper($db);
    $result = $applicationMapper->save($application);
    if (!$result) {
      return FALSE;
    }

    $application = $applicationMapper->findByName($name);
    $appId = $application->getAppId();
    if (!$appId) {
      return FALSE;
    }

    return $appId;
  }

}