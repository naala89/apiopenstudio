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
   * @var Db\Application
   */
  private $application;

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
      throw new ApiException('Failed to connect to the database.');
    }
  }

  /**
   * Get the stored application.
   *
   * @return array
   *   Application array.
   */
  public function getApplication() {
    return $this->application->dump();
  }

  /**
   * Create an application using an account ID and app name.
   *
   * @param string $accId
   *   ID of the account.
   * @param string $name
   *   Name of the application.
   *
   * @return array
   *   Application.
   *
   * @throws ApiException
   */
  public function createByAccIdName($accId, $name) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $application = $applicationMapper->findByAccIdName($accId, $name);
    if (!empty($application->getAccId())) {
      throw new ApiException('Application already exists.');
    }

    $application = new Db\Application(
      NULL,
      $accId,
      $name
    );
    $applicationMapper->save($application);
    $this->application = $applicationMapper->findByAccIdName($accId, $name);

    return $this->getApplication();
  }

  /**
   * Create an application using a user account ID and app name.
   *
   * @param string $uaid
   *   User acount ID.
   * @param string $name
   *   Name of the application.
   *
   * @return array
   *   Application.
   */
  public function createByUserAccIdName($uaid, $name) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->createByAccIdName($userAccount->getAccId(), $name);
  }

  /**
   * Find an application by its application ID.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   Application.
   */
  public function findByApplicationId($appId) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $this->application = $applicationMapper->findByAppId($appId);
    return $this->getApplication();
  }

  /**
   * Find all an application by account ID and application name.
   *
   * @param int $accId
   *   ID of the account.
   * @param int $appName
   *   The application name.
   *
   * @return array
   *   Application.
   */
  public function findByAccIdAppName($accId, $appName) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $this->application = $applicationMapper->findByAccIdName($accId, $appName);
    return $this->getApplication();
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
      $applications[$application['appid']] = $application;
    }

    return $applications;
  }

  /**
   * Find all an applications for an account by user account ID.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return array
   *   Array of associative arrays of applications, indexed by appId.
   */
  public function findByUserAccountId($uaid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->findByAccountId($userAccount->getAccId());
  }

  /**
   * Update an application name.
   *
   * @param string $appName
   *   Name of the application.
   *
   * @return array
   *   Application.
   *
   * @throws ApiException
   */
  public function update($appName) {
    if (empty($this->application->getAppId())) {
      throw new ApiException('Cannot update application, none fetchjed yet.');
    }
    $this->application->setName($appName);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $applicationMapper->save($this->application);

    return $this->getApplication();
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
