<?php

namespace Datagator\Admin;

use Datagator\Db;

class Application
{
  private $dbSettings;
  private $db;

  /**
   * Application constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings)
  {
    $this->dbSettings = $dbSettings;

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
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Get all an applications for an account.
   *
   * @param int $accId
   *   ID of the account.
   *
   * @return array
   */
  public function getByAccount($accId) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $results = $applicationMapper->findByAccId($accId);
    $applications = [];
    foreach ($results as $result) {
      $applications[] = $result->dump();
    }

    return $applications;
  }

  /**
   * Create an application.
   *
   * @param string $accId
   *   ID of the account.
   * @param string $name
   *   Name of the application.
   *
   * @return bool|int
   */
  public function create($accId, $name) {
    $application = new Db\Application(
      NULL,
      $accId,
      $name
    );

    $applicationMapper = new Db\ApplicationMapper($this->db);
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

  /**
   * Update an application.
   *
   * @param string $appId
   *   ID of the application.
   * @param string $appName
   *   Name of the application.
   *
   * @return bool|int
   */
  public function update($appId, $appName) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $application = $applicationMapper->findByAppId($appId);
    $application->setName($appName);
    $result = $applicationMapper->save($application);

    if (!$result) {
      return FALSE;
    }
    return $appId;
  }

  /**
   * Delete an application.
   *
   * @param string $appId
   *   ID of the application.
   *
   * @return bool|int
   */
  public function delete($appId) {
    $application = new Db\Application($appId, NULL, NULL);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    return $applicationMapper->delete($application);
  }

}