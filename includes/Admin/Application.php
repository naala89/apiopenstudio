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
      throw new ApiException($this->db->ErrorMsg());
    }
  }

  public function getApplication() {
    return $this->application;
  }

  /**
   * Create an application using an account ID and app name.
   *
   * @param string $accId
   *   ID of the account.
   * @param string $name
   *   Name of the application.
   *
   * @return bool|int
   *   Success or failure of the operation.
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

    return $this->application->dump();
  }

  /**
   * Create an application using a user account ID and app name.
   *
   * @param string $uaid
   *   User acount ID.
   * @param string $name
   *   Name of the application.
   *
   * @return bool|int
   *   Success or failure of the operation.
   */
  public function createByUserAccIdName($uaid, $name) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->createByAccIdName($userAccount->getAccId(), $name);
  }

  /**
   * @param $appId
   * @return Db\Application
   */
  public function findByApplicationId($appId) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    return $applicationMapper->findByAppId($appId);
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
    try {
      $userAccountMapper = new Db\UserAccountMapper($this->db);
      $userAccount = $userAccountMapper->findByUaid($uaid);
      return $this->findByAccountId($userAccount->getAccId());
    } catch (ApiException $e) {
      return FALSE;
    }
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
