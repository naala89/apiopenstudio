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
    try {
      $applicationMapper = new Db\ApplicationMapper($this->db);
      $application = $applicationMapper->findByAccIdName($accId, $name);
      if (!empty($application->getAccId())) {
        // Application already exists.
        return 'Application already exists.';
      }
    } catch (ApiException $e) {
      return 'An error occurred at the DB level, please check the logs.';
    }

    try {
      $application = new Db\Application(
        NULL,
        $accId,
        $name
      );
      $applicationMapper->save($application);
    } catch (ApiException $e) {
      return 'An error occurred at the DB level, please check the logs.';
    }

    return TRUE;
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
    try {
      $applicationMapper = new Db\ApplicationMapper($this->db);
      $results = $applicationMapper->findByAccId($accId);
    } catch (ApiException $e) {
      return FALSE;
    }
    $applications = [];
    foreach ($results as $result) {
      $application = $result->dump();
      $applications[$application['appId']] = $application;
    }

    return $applications;
  }

  /**
   * Find all an applications for an account.
   *
   * @param int $accId
   *   ID of the account.
   * @param int $appName
   *   The application name.
   *
   * @return array
   *   Array of associative arrays of applications, indexed by appId.
   */
  public function findByAccIdAppName($accId, $appName) {
    try {
      $applicationMapper = new Db\ApplicationMapper($this->db);
      $result = $applicationMapper->findByAccIdName($accId, $appName);
    } catch (ApiException $e) {
      return FALSE;
    }

    return  $result->dump();
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
    try {
      $application = $applicationMapper->findByAppId($appId);
    } catch (ApiException $e) {
      return FALSE;
    }
    try {
      $application->setName($appName);
    } catch (ApiException $e) {
      return FALSE;
    }

    try {
      $applicationMapper->save($application);
    } catch (ApiException $e) {
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
    try {
      return $applicationMapper->delete($application);
    } catch (ApiException $e) {
      return FALSE;
    }
  }

}
