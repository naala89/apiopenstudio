<?php

namespace Datagator\Admin;

use Datagator\Core\ApiException;
use Datagator\Db;

/**
 * Class UserAccount
 *
 * @package Datagator\Admin
 */
class UserAccount {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Datagator\Db\UserAccount
   */
  private $userAccount;

  /**
   * User constructor.
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
   * Get the stored user account.
   * @return mixed
   */
  public function getUserAccount() {
    return $this->userAccount->dump();
  }

  /**
   * Create a new user account.
   *
   * @param int $accid
   *   Account ID.
   * @param int $uid
   *   User ID.
   *
   * @return bool
   *   Success.
   */
  public function create($accid, $uid) {
    $userAccount = new Db\UserAccount(
      NULL,
      $uid,
      $accid
    );
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    return $userAccountMapper->save($userAccount);
  }

  /**
   * Delete a user account.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
  public function delete() {
    if (empty($this->userAccount->getUaid())) {
      throw new ApiException('Invalid user account specified.');
    }

    // Delete roles.
    $userAccountRoleMapper = new Db\UserAccountRoleMapper($this->db);
    $roles = $userAccountRoleMapper->findByUaid($this->userAccount->getUaid());
    foreach ($roles as $role) {
      $userAccountRoleMapper->delete($role);
    }

    // Delete user account.
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccountMapper->delete($this->userAccount);

    return TRUE;
  }

  /**
   * Find a user account by user account ID.
   *
   * @param int $uaid
   *   user account ID.
   *
   * @return array|bool
   *   User account or FALSE on exception.
   */
  public function findByUaid($uaid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $this->userAccount = $userAccountMapper->findByUaid($uaid);
    return $this->getUserAccount();
  }

  /**
   * Find a user account by user ID and account ID.
   *
   * @param int $uid
   *   user ID.
   * @param int $accId
   *   Account ID.
   *
   * @return array|bool
   *   User account or FALSE on exception.
   */
  public function findByUidAccId($uid, $accId) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $this->userAccount = $userAccountMapper->findByUidAccId($uid, $accId);
    return $this->userAccount->dump();
  }

  /**
   * Find all user accounts by account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return array
   *   Array of mapped UserAccount objects indexed by user account ID.
   */
  public function findByAccountId($accid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccounts = $userAccountMapper->findByAccId($accid);
    $result = [];
    foreach ($userAccounts as $userAccount) {
      $result[$userAccount->getUaid()] = $userAccount->dump();
    }
    return $result;
  }

  /**
   * Find all user accounts by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserAccount objects indexed by user account ID.
   */
  public function findByUserId($uid) {
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccounts = $userAccountMapper->findByUid($uid);
    $result = [];
    foreach ($userAccounts as $userAccount) {
      $result[$userAccount->getUaid()] = $userAccount->dump();
    }
    return $result;
  }

  /**
   * Find all roles for a user account.
   *
   * @param int $uaid
   *   User account ID.
   *
   * @return array
   *   Array of mapped UserAccountRole objects indexed by user account role ID.
   */
  public function findAllRolesByUaid($uaid) {
    $userAccountRoleMapper = new Db\UserAccountRoleMapper($this->db);
    $userAccountRoles = $userAccountRoleMapper->findByUaid($uaid);
    $result = [];
    foreach ($userAccountRoles as $userAccountRole) {
      $result[$userAccountRole->getUarid()] = $userAccountRole->dump();
    }
    return $result;
  }

}
