<?php

namespace Datagator\Admin;

use Datagator\Core\ApiException;
use Datagator\Db;

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
   * Application constructor.
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
      throw new ApiException($this->db->ErrorMsg());
    }
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
    try {
      $applicationMapper->save($application);
    } catch (ApiException $e) {
      return FALSE;
    }

    $application = $applicationMapper->findByName($name);
    $appId = $application->getAppId();
    return empty($appId) ? FALSE : $appId;
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
  public function findByAccountId($accId) {
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

    try {
      $applicationMapper->save($application);
    } catch (ApiException $e) {
      return FALSE;
    }

    if (!$result) {
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
