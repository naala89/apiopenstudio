<?php

namespace Datagator\Admin;

use Datagator\Db\AccountMapper;
use Datagator\Db\Application;
use Datagator\Db\ApplicationUserRoleMapper;
use Datagator\Db\RoleMapper;
use Datagator\Core\ApiException;
use Datagator\Db\UserAccountRoleMapper;

/**
 * Class ApplicationUserRole.
 *
 * @package Datagator\Admin
 */
class ApplicationUserRole {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Datagator\Db\ApplicationUserRole
   */
  private $applicationUserRole;

  /**
   * ApplicationUserRole constructor.
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
   * Get the stored application user role.
   *
   * @return array
   *   Application User Role.
   */
  public function getApplicationUserRole() {
    return $this->applicationUserRole->dump();
  }

  /**
   * Set the stored application user role.
   *
   * @param array $applicationUserRole
   *   Application user role.
   *
   * @return array
   *   Application User Role.
   */
  public function setApplicationUserRole(array $applicationUserRole) {
    $this->applicationUserRole = new \Datagator\Db\ApplicationUserRole(
      $applicationUserRole['aurid'],
      $applicationUserRole['appid'],
      $applicationUserRole['uid'],
      $applicationUserRole['rid']
    );
    return $this->applicationUserRole->dump();
  }

  /**
   * Delete an application user role.
   *
   * @param array|NULL $applicationUserRole
   *   Application user role.
   *
   * @return bool
   *    Success.
   */
  public function delete(array $applicationUserRole = NULL) {
    if ($applicationUserRole !== NULL ) {
      $this->setApplicationUserRole($applicationUserRole);
    }
    $applicationUserRoleMapper = new ApplicationUserRoleMapper($this->db);
    return $applicationUserRoleMapper->delete($this->applicationUserRole);
  }

  /**
   * Find all application user roles.
   *
   * @return array
   *   Array of roles.
   */
  public function findAll() {
    $applicationUserRoleMapper = new ApplicationUserRoleMapper($this->db);
    $results = $applicationUserRoleMapper->findAll();

    $applicationUserRoles = [];
    foreach ($results as $result) {
      $applicationUserRole = $result->dump();
      $applicationUserRoles[$applicationUserRole['rid']] = $applicationUserRole;
    }

    return $applicationUserRoles;
  }

  /**
   * Find an application user role by its ID.
   *
   * @param int $aurid
   *   Application user role ID.
   *
   * @return array
   *   ApplicationUserRole.
   */
  public function findByAurid($aurid) {
    $applicationUserRoleMapper = new ApplicationUserRoleMapper($this->db);
    $this->applicationUserRole = $applicationUserRoleMapper->findByAurid($aurid);
    return $this->getRole();
  }

  /**
   * Find by application ID.
   *
   * @param int $appid
   *   Account ID.
   *
   * @return array
   *   Array of ApplicationUserRoles indexed by aurid.
   */
  public function findByAppid($appid) {
    $applicationUserRoleMapper = new ApplicationUserRoleMapper($this->db);
    $applicationUserRoles = [];
    $results = $applicationUserRoleMapper->findByAppid($appid);
    foreach ($results as $result) {
      $applicationUserRole = $result->dump();
      $applicationUserRoles[$applicationUserRole['aurid']] = $applicationUserRole;
    }
    return $applicationUserRoles;
  }

  /**
   * Find all accounts that a user has a role with.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of Accounts indexed by accid.
   */
  public function findAccountsByUid($uid) {
    $applicationUserRoleMapper = new ApplicationUserRoleMapper($this->db);
    $accountMapper = new AccountMapper($this->db);
    $applicationUserRoles = $applicationUserRoleMapper->findByUid($uid);
    $accounts = [];
    foreach ($applicationUserRoles as $applicationUserRole) {
      $accid = $applicationUserRole->getAccid();
      if (!isset($accounts[$accid])) {
        $account = $accountMapper->findByAccid($accid);
        $accounts[$accid] = $account->dump();
      }
    }
    return $accounts;
  }

}
