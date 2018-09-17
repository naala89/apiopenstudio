<?php

namespace Datagator\Admin;

use Datagator\Core\ApiException;
use Datagator\Db;
use Datagator\Core\Utilities;
use Datagator\Core\Hash;

/**
 * Class User.
 *
 * @package Datagator\Admin
 */
class User{

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Datagator\Db\User
   */
  private $user;

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
   * Get the stored user.
   *
   * @return array
   *   User array.
   */
  public function getUser() {
    return $this->user->dump();
  }

  /**
   * Log a user in.
   *
   * @param string $accountName
   *   Account bane.
   * @param string $username
   *   User name.
   * @param string $password
   *   User password.
   * @param string $ttl
   *   Token life. Example: '+1 hour'.
   *
   * @return array|bool
   */
  public function adminLogin($accountName, $username, $password, $ttl) {
    // Validate account and get account ID.
    $accountMapper = new Db\AccountMapper($this->db);
    $account = $accountMapper->findByName($accountName);
    if (empty($accId = $account->getAccId())) {
      return FALSE;
    }

    // Validate username and get user ID.
    $userMapper = new Db\UserMapper($this->db);
    $this->user = $userMapper->findByUsername($username);
    if (empty($uid = $this->user->getUid())) {
      return FALSE;
    }

    // Validate user is assigned to the account as owner or user of any account applications.
    $accountOwnerMapper = new Db\AccountOwnerMapper($this->db);
    $accountOwner = $accountOwnerMapper->findByAccidUid($accId, $uid);
    $validUser = !empty($accountOwner->getAoid());
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $applicationUserMapper = new Db\ApplicationUserRoleMapper($this->db);
    $applications = $applicationMapper->findByAccId($accId);
    foreach ($applications as $application) {
      $applicationUser = $applicationUserMapper->findByUid($uid);
      $validUser = !empty($applicationUser['auid']) ? TRUE : $validUser;
    }
    if (!$validUser) {
      return FALSE;
    }

    // Set up salt if not defined.
    if ($this->user->getSalt() == NULL) {
      $this->user->setSalt(Hash::generateSalt());
    }

    // Generate password hash and compare to stored hash.
    $hash = Hash::generateHash($password, $this->user->getSalt());
    if ($this->user->getHash() != NULL && $this->user->getHash() != $hash) {
      return FALSE;
    }

    // If token exists and is active, return it.
    if (!empty($this->user->getToken())
      && !empty($this->user->getTokenTtl())
      && Utilities::date_mysql2php($this->user->getTokenTtl()) > time()) {
      $this->user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
      return [
        'token' => $this->user->getToken(),
        'accid' => $accId,
        'uid' => $uid,
      ];
    }

    // Perform login.
    $this->user->setHash($hash);
    $token = Hash::generateToken($username);
    $this->user->setToken($token);
    $this->user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
    try {
      $userMapper->save($this->user);
    } catch (ApiException $e) {
      return FALSE;
    }

    return [
      'token' => $this->user->getToken(),
      'accid' => $accId,
      'uid' => $uid,
    ];
  }

  /**
   * Create a user.
   *
   * @param string $username
   *   User name.
   * @param string $password
   *   User password.
   * @param string $email
   *   User email.
   * @param string $honorific
   *   User honorific.
   * @param string $nameFirst
   *   User first name.
   * @param string $nameLast
   *   User last name.
   * @param string $company
   *   User company.
   * @param string $website
   *   User website.
   * @param string $addressStreet
   *   User address street.
   * @param string $addressSuburb
   *   User address suburb.
   * @param string $addressCity
   *   User address city.
   * @param string $addressState
   *   User address state.
   * @param string $addressCountry
   *   User address country.
   * @param string $addressPostcode
   *   User address postcode.
   * @param string $phoneMobile
   *   User mobile phone number.
   * @param string $phoneWork
   *   User work phone number.
   *
   * @return bool|array
   *   False or associative user array.
   */
  public function create($username, $password, $email = NULL, $honorific = NULL, $nameFirst = NULL, $nameLast = NULL, $company = NULL, $website = NULL, $addressStreet = NULL, $addressSuburb = NULL, $addressCity = NULL, $addressState = NULL, $addressCountry = NULL, $addressPostcode = NULL, $phoneMobile = NULL, $phoneWork = NULL) {
    $user = new Db\User(
      NULL,
      1,
      $username,
      NULL,
      NULL,
      NULL,
      NULL,
      $email,
      $honorific,
      $nameFirst,
      $nameLast,
      $company,
      $website,
      $addressStreet,
      $addressSuburb,
      $addressCity,
      $addressState,
      $addressCountry,
      $addressPostcode,
      $phoneMobile,
      $phoneWork
    );
    $user->setPassword($password);

    $userMapper = new Db\UserMapper($this->db);

    try {
      $userMapper->save($user);
    } catch (ApiException $e) {
      return FALSE;
    }
    $this->user = $userMapper->findByUsername($username);
    return empty($this->user->getUid()) ? FALSE: $this->user->dump();
  }

  /**
   * Find a user by their user ID.
   *
   * @param string $uid
   *   User ID.
   *
   * @return array|bool
   *   FALSE | associative array of the user.
   */
  public function findByUserId($uid) {
    $userMapper = new Db\UserMapper($this->db);

    try {
      $this->user = $userMapper->findByUid($uid);
    } catch (ApiException $e) {
      return FALSE;
    }
    return empty($this->user->getUid()) ? FALSE : $this->user->dump();
  }

  /**
   * Find a user by their email.
   *
   * @param string $email
   *   User email.
   *
   * @return array|bool
   *   FALSE | associative array of the user.
   */
  public function findByEmail($email) {
    $userMapper = new Db\UserMapper($this->db);

    try {
      $this->user = $userMapper->findByEmail($email);
    } catch (ApiException $e) {
      return FALSE;
    }
    return $this->user->dump();
  }

  /**
   * Find a user by their username.
   *
   * @param string $username
   *   User username.
   *
   * @return array|bool
   *   FALSE | associative array of the user.
   */
  public function findByUsername($username) {
    $userMapper = new Db\UserMapper($this->db);

    try {
      $this->user = $userMapper->findByUsername($username);
    } catch (ApiException $e) {
      return FALSE;
    }
    return $this->user->dump();
  }

  /**
   * Assign sysadmin role to current user.
   *
   * @return bool
   *   Success.
   */
  public function assignSysadmin() {
    $sysadminMapper = new Db\SysadminMapper($this->db);
    $sysadmin = new Db\Sysadmin(
      NULL,
      $this->user->getUid()
    );
    try {
      return $sysadminMapper->save($sysadmin);
    } catch (ApiException $e) {
      return FALSE;
    }
  }

  /**
   * Assign the user to an account by the account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return array|bool
   *   FALSE | user account associative array.
   */
  public function assignToAccountId($accid) {
    if (empty($this->user) || empty($this->user->getUid())) {
      return FALSE;
    }

    $accountMapper = new Db\AccountMapper($this->db);
    try {
      $account = $accountMapper->findByAccId($accid);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($account->getAccId())) {
      return FALSE;
    }

    $userAccount = new Db\UserAccount(NULL, $this->user->getUid(), $accid);
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    try {
      $userAccountMapper->save($userAccount);
      $userAccount = $userAccountMapper->findByUidAccId($this->user->getUid(), $accid);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($userAccount->getUaid())) {
      return FALSE;
    }

    return $userAccount->dump();
  }

  /**
   * Assign the user to an account by the account name.
   *
   * @param string $accountName
   *   Account name.
   *
   * @return array|bool
   *   FALSE | user account associative array.
   */
  public function assignToAccountName($accountName) {
    $accountMapper = new Db\AccountMapper($this->db);
    try {
      $account = $accountMapper->findByName($accountName);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($account->getAccId())) {
      return FALSE;
    }
    return $this->assignToAccountId($account->getAccId());
  }

  /**
   * Assign a role to a user for an account.
   *
   * @param string $roleName
   *   Role name.
   * @param string $accountName
   *   Account name.
   * @param string $applicationName
   *   Application name.
   *
   * @return bool
   *   User account role associative array.
   */
  public function assignRole($roleName, $accountName, $applicationName = NULL) {
    // Find the role.
    $roleMapper = new Db\RoleMapper($this->db);
    try {
      $role = $roleMapper->findByName($roleName);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($role->getRid())) {
      return FALSE;
    }

    // Find the account.
    $accountMapper = new Db\AccountMapper($this->db);
    try {
      $account = $accountMapper->findByName($accountName);
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($account->getAccId())) {
      return FALSE;
    }

    // Find the user account.
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    try {
      $userAccount = $userAccountMapper->findByUidAccId($this->user->getUid(), $account->getAccId());
    } catch (ApiException $e) {
      return FALSE;
    }
    if (empty($userAccount->getUaid())) {
      return FALSE;
    }

    // Find the application.
    $appid = NULL;
    if (!empty($applicationName)) {
      $applicationMapper = new Db\ApplicationMapper($this->db);
      try {
        $application = $applicationMapper->findByAccIdName($account->getAccId(), $applicationName);
      } catch (ApiException $e) {
        return FALSE;
      }
      $appid = $application->getAppId();
    }

    // Save the new user account role.
    $userAccountRole = new Db\UserAccountRole(NULL, $userAccount->getUaid(), $role->getRid(), $appid);
    $userAccountRoleMapper = new Db\UserAccountRoleMapper($this->db);
    try {
      $userAccountRoleMapper->save($userAccountRole);
    } catch (ApiException $e) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Find roles for a user ID in an account.
   *
   * @param int $accid.
   *   Account ID.
   *
   * @return array
   *   Array of mapped UserAccountRole objects.
   */
  public function findRolesByAccid($accid) {
    // Find roles for the user.
    $accountOwnerMapper = new Db\AccountOwnerMapper($this->db);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $roleMapper = new Db\RoleMapper($this->db);
    $applicationUserRoleMapper = new Db\ApplicationUserRoleMapper($this->db);
    $roles = $allRoles = [];
    try {
      $uid = $this->user->getUid(); // Current uid.
      // All roles indexed by rid.
      $results = $roleMapper->findAll();
      foreach ($results as $result) {
        $allRoles[$result->getRid()] = $result->dump();
      }
      // Check account_owner table;
      $accountOwner = $accountOwnerMapper->findByAccidUid($accid, $uid);
      if (!empty($accountOwner->getAoid())) {
        $roles[] = 'Owner';
      }
      // Fined user roles for each application.
      $applications = $applicationMapper->findByAccid($accid);
      foreach ($applications as $application) {
        $applicationUserRoles = $applicationUserRoleMapper->findByAppidUid($application->getAppid(), $uid);
        if (empty($applicationUserRoles)) {
          continue;
        }
        foreach ($applicationUserRoles as $applicationUserRole) {
          $roleName = $allRoles[$applicationUserRole->getRid()]['name'];
          if (!in_array($roleName)) {
            $roles[] = $roleName;
          }
        }
      }
    } catch (ApiException $e) {
      return [];
    }

    return $roles;
  }


}
