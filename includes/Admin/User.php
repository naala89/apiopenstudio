<?php

namespace Gaterdata\Admin;

use Gaterdata\Core\ApiException;
use Gaterdata\Db;
use Gaterdata\Core\Utilities;
use Gaterdata\Core\Hash;

/**
 * Class User.
 *
 * @package Gaterdata\Admin
 */
class User {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Gaterdata\Db\User
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
   * @param string $username
   *   User name.
   * @param string $password
   *   User password.
   * @param string $ttl
   *   Token life. Example: '+1 hour'.
   *
   * @return array
   *   login token and uid.
   *
   * @throws ApiException
   */
  public function adminLogin($username, $password, $ttl) {
    // Validate username and get user ID.
    $userMapper = new Db\UserMapper($this->db);
    $this->user = $userMapper->findByUsername($username);
    if (empty($uid = $this->user->getUid()) || $this->user->getActive() != 1) {
      throw new ApiException('Invalid user or password');
    }

    // Generate password hash and compare to stored hash.
    if ($this->user->getHash() != NULL && password_verify($password, $this->user->getHash()) {
      throw new ApiException('Invalid user or password');
    }

    // If token exists and is active, return it.
    if (!empty($this->user->getToken())
      && !empty($this->user->getTokenTtl())
      && Utilities::date_mysql2php($this->user->getTokenTtl()) > time()) {
      $this->user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
      return [
        'token' => $this->user->getToken(),
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
      throw new ApiException($e->getMessage());
    }

    return [
      'token' => $this->user->getToken(),
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
   * @return array
   *   associative user array.
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
    $userMapper->save($user);
    $this->user = $userMapper->findByUsername($username);
    return $this->user->dump();
  }

  /**
   * Find all users.
   *
   * @return array
   *   Array of users, indexed bu uid.
   */
  public function findAll() {
    $userMapper = new Db\UserMapper($this->db);
    $results = $userMapper->findAll();
    $users = [];
    foreach ($results as $result) {
      $user = $result->dump();
      $users[$user['uid']] = $user;
    }
    return $users;
  }

  /**
   * Find a user by their user ID.
   *
   * @param string $uid
   *   User ID.
   *
   * @return array
   *   associative array of the user.
   *
   * @throws ApiException
   */
  public function findByUserId($uid) {
    $userMapper = new Db\UserMapper($this->db);
    $this->user = $userMapper->findByUid($uid);
    return $this->user->dump();
  }

  /**
   * Find a user by their email.
   *
   * @param string $email
   *   User email.
   *
   * @return array
   *   associative array of the user.
   */
  public function findByEmail($email) {
    $userMapper = new Db\UserMapper($this->db);
    $this->user = $userMapper->findByEmail($email);
    return $this->user->dump();
  }

  /**
   * Find a user by their username.
   *
   * @param string $username
   *   User username.
   *
   * @return array
   *   associative array of the user.
   */
  public function findByUsername($username) {
    $userMapper = new Db\UserMapper($this->db);
    $this->user = $userMapper->findByUsername($username);
    return $this->user->dump();
  }

  /**
   * Assign Administrator role to current user.
   *
   * @return bool
   *   Success.
   */
  public function assignAdministrator() {
    $administratorMapper = new Db\AdministratorMapper($this->db);
    $administrator = new Db\Administrator(
      NULL,
      $this->user->getUid()
    );
    return $administratorMapper->save($administrator);
  }

  /**
   * Validate if a user had administrator status.
   *
   * @return bool
   *   Is an admin.
   */
  public function isAdministrator() {
    $administratorMapper = new Db\AdministratorMapper($this->db);
    $administrator = $administratorMapper->findByUid($this->user->getUid());
    return !empty($administrator);
  }

  /**
   * Validate if a user had manager status.
   *
   * @return bool
   *   Is an admin.
   */
  public function isManager() {
    $managerMapper = new Db\ManagerMapper($this->db);
    $manager = $managerMapper->findByUid($this->user->getUid());
    return !empty($manager);
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
   * Find roles for a user.
   *
   * @return array
   *   Array of mapped UserAccountRole objects.
   */
  public function findRoles() {
    try {
      // Find roles for the user.
      $roleMapper = new Db\RoleMapper($this->db);
      $applicationUserRoleMapper = new Db\ApplicationUserRoleMapper($this->db);
      $roles = $allRoles = [];
      $uid = $this->user->getUid(); // Current uid.
      // All roles indexed by rid.
      $results = $roleMapper->findAll();
      foreach ($results as $result) {
        $allRoles[$result->getRid()] = $result->dump();
      }
      // Find user roles.
      $applicationUserRoles = $applicationUserRoleMapper->findByUid($uid);
      foreach ($applicationUserRoles as $applicationUserRole) {
        $roleName = $allRoles[$applicationUserRole->getRid()]['name'];
        if (!in_array($roleName)) {
          $roles[] = $roleName;
        }
      }
    } catch (ApiException $e) {
      return [];
    }
    return $roles;
  }


}
