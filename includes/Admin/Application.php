<?php

namespace Datagator\Admin;

use Datagator\Db;
use Monolog\Logger;

class Application {

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
   * Application constructor.
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
   * Find all an applications for an account.
   *
   * @param int $accId
   *   ID of the account.
   *
   * @return array
   *   Array of associative arrays of applications, indexed by appId.
   */
  public function findByAccount($accId) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $results = $applicationMapper->findByAccId($accId);
    $applications = [];
    foreach ($results as $result) {
      $application = $result->dump();
      $applications[$application['appId']] = $application;
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
   *   Success or failure of the operation.
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
   * Update an application name.
   *
   * @param string $appId
   *   ID of the application.
   * @param string $appName
   *   Name of the application.
   *
   * @return bool|int
   *   Success or failure of the operation.
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
   *   Success or failure of the operation.
   */
  public function delete($appId) {
    $application = new Db\Application($appId, NULL, NULL);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    return $applicationMapper->delete($application);
  }

}
