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
   * Set the stored application.
   *
   * @param array $application
   *   Application.
   *
   * @return array
   *   Application
   */
  public function setApplication(array $application) {
    $application = new Db\Application($application['appid'], $application['accid'], $application['name']);
    $this->application = $application;
    return $this->getApplication();
  }

  /**
   * Create an application.
   *
   * @param string $accid
   *   Account ID.
   * @param string $name
   *   Application name.
   *
   * @return array
   *   Application.
   *
   * @throws ApiException
   */
  public function create($accid, $name) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $application = $applicationMapper->findByAccIdName($accid, $name);
    if (!empty($application->getAccId())) {
      throw new ApiException('Application already exists.');
    }

    $application = new Db\Application(
      NULL,
      $accid,
      $name
    );
    $applicationMapper->save($application);
    $this->application = $applicationMapper->findByAccIdName($accid, $name);

    return $this->getApplication();
  }

  /**
   * Update an application.
   *
   * @param array|NULL $application
   *   Application.
   *
   * @return array
   *   Application.
   *
   * @throws ApiException
   */
  public function update(array $application = NULL) {
    if ($application !== NULL) {
      $this->setApplication($application);
    }
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $applicationMapper->save($this->application);
    return $this->getApplication();
  }

  /**
   * Delete an application.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
  public function delete() {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    return $applicationMapper->delete($this->application);
  }

  /**
   * Find an application by its application ID.
   *
   * @param int $appid
   *   Application ID.
   *
   * @return array
   *   Application.
   *
   * @throws ApiException
   */
  public function findByApplicationId($appid) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $this->application = $applicationMapper->findByAppId($appid);
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
   * Find all applications.
   *
   * @return array | boolean
   *   Array of associative arrays of applications, indexed by appid.
   */
  public function findAll() {
    $applications = [];
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $results = $applicationMapper->findAll();
    foreach ($results as $result) {
      $application = $result->dump();
      $applications[$application['appid']] = $application;
    }
    return $applications;
  }

  /**
   * Find all an applications for an account.
   *
   * @param int $accid
   *   ID of the account.
   *
   * @return array
   *   Array of associative arrays of applications, indexed by appid.
   */
  public function findByAccid($accid) {
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $results = $applicationMapper->findByAccid($accid);

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
   *   Array of associative arrays of applications, indexed by appid.
   */
  public function findByUserAccountId($uaid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUaid($uaid);
    $results = $this->findByAccountId($userAccount->getAccId());

    $applications = [];
    foreach ($results as $result) {
      $applications[$result['appid']] = $result;
    }

    return $applications;
  }

  /**
   * Find all an user roles for an application by application ID.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   Array of associative arrays of user application roles, indexed by uarid.
   */
  public function findUserRoles($appId=null) {
    $userAccountRolesMapper = new Db\UserAccountRoleMapper($this->db);
    $appId = !empty($sppId) ? $appId : $this->application->getAppId();
    $results = $userAccountRolesMapper->findByApplicationId($appId);

    $roles = [];
    foreach ($results as $result) {
      $application = $result->dump();
      $roles[$application['uarid']] = $application;
    }

    return $roles;
  }

}
