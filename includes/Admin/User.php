<?php

namespace Datagator\Admin;

use Datagator\Core\ApiException;
use Datagator\Db;
use Monolog\Logger;
use Datagator\Core\Utilities;
use Datagator\Core\Hash;

/**
 * Class User.
 *
 * @package Datagator\Admin
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
   * @var \Monolog\Logger
   */
  private $logger;
  /**
   * @var \Datagator\Db\User
   */
  private $user;

  /**
   * User constructor.
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
   * @return mixed
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
   *   False or user/account details.
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
    $user = $userMapper->findByUsername($username);
    if (empty($uid = $user->getUid())) {
      return FALSE;
    }

    // Validate user account and het user account ID.
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUidAccId($uid, $accId);
    if (empty($uaid = $userAccount->getUaid())) {
      return FALSE;
    }

    // Set up salt if not defined.
    if ($user->getSalt() == NULL) {
      $user->setSalt(Hash::generateSalt());
    }

    // Generate password hash and compare to stored hash.
    $hash = Hash::generateHash($password, $user->getSalt());
    if ($user->getHash() != NULL && $user->getHash() != $hash) {
      return FALSE;
    }

    // If token exists and is active, return it.
    if (!empty($user->getToken())
      && !empty($user->getTokenTtl())
      && Utilities::date_mysql2php($user->getTokenTtl()) > time()) {
      $user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
      return [
        'token' => $user->getToken(),
        'uaid' => $userAccount->getUaid(),
      ];
    }

    // Perform login.
    $user->setHash($hash);
    $token = Hash::generateToken($username);
    $user->setToken($token);
    $user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
    $userMapper->save($user);

    $this->user = $user;
    return $this->user->dump();
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
    $result = $userMapper->save($user);
    if (!$result) {
      return FALSE;
    }
    $this->user = $userMapper->findByUsername($username);
    if (empty($user->getUid())) {
      return FALSE;
    }
    return $this->user->dump();
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
    $user = $userMapper->findByUid($uid);

    if (empty($user->getUid())) {
      return FALSE;
    }

    $this->user = $user;
    return $this->user->dump();
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
    $user = $userMapper->findByEmail($email);

    if (empty($user->getUid())) {
      return FALSE;
    }

    $this->user = $user;
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
    $user = $userMapper->findByUsername($username);

    if (empty($user->getUid())) {
      return FALSE;
    }

    $this->user = $user;
    return $this->user->dump();
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
    $account = $accountMapper->findByAccId($accid);
    if (empty($account->getAccId())) {
      return FALSE;
    }
    $userAccount = new Db\UserAccount(NULL, $this->user->getUid(), $accid);
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    try {
      $userAccountMapper->save($userAccount);
    } catch (ApiException $e) {
      return FALSE;
    }

    $userAccount = $userAccountMapper->findByUidAccId($this->user->getUid(), $accid);
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
    $account = $accountMapper->findByName($accountName);
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
   * @return array|bool
   *   FALSE | user account role associative array.
   */
  public function assignRole($roleName, $accountName, $applicationName = NULL) {
    // Find the role.
    $roleMapper = new Db\RoleMapper($this->db);
    $role = $roleMapper->findByName($roleName);
    if (empty($role->getRid())) {
      return FALSE;
    }

    // Find the account.
    $accountMapper = new Db\AccountMapper($this->db);
    $account = $accountMapper->findByName($accountName);
    if (empty($account->getAccId())) {
      return FALSE;
    }

    // Find the user account.
    $userAccountMapper = new Db\UserAccountMapper($this->db);
    $userAccount = $userAccountMapper->findByUidAccId($this->user->getUid(), $account->getAccId());
    if (empty($userAccount->getUaid())) {
      return FALSE;
    }

    // Find the application.
    $appid = NULL;
    if (!empty($applicationName)) {
      $applicationMapper = new Db\ApplicationMapper($this->db);
      $application = $applicationMapper->findByAccIdName($account->getAccId(), $applicationName);
      $appid = $application->getAppId();
    }

    // Save the new user account role.
    $userAccountRole = new Db\UserAccountRole(NULL, $userAccount->getUaid(), $role->getRid(), $appid);
    if (empty($userAccountRole->getUarid())) {
      return FALSE;
    }
    return $userAccountRole->dump();
  }

}
