<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Core\Utilities;
use Datagator\Core\Hash;

/**
 * Class User.
 *
 * @package Datagator\Admin
 */
class User {

  private $dbSettings;
  private $db;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

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
        'accountName' => $account->getName(),
        'accountId' => $account->getAccId(),
        'userAccountId' => $userAccount->getUaid(),
      ];
    }

    // Perform login.
    $user->setHash($hash);
    $token = Hash::generateToken($username);
    $user->setToken($token);
    $user->setTokenTtl(Utilities::date_php2mysql(strtotime($ttl)));
    $userMapper->save($user);

    return [
      'token' => $token,
      'accountName' => $account->getName(),
      'accountId' => $account->getAccId(),
      'userAccountId' => $userAccount->getUacid(),
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
   * @return bool|int
   *   False or account ID.
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
    $user = $userMapper->findByUsername($username);
    return $user->getUid();
  }

  /**
   * Find all users associated with an account.
   *
   * @param int $accId
   *   Account ID.
   *
   * @return array
   *   Array of users.
   */
  public function findByAccount($accId) {
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    return $userRoleMapper->findByAccId($accId);
  }

  /**
   * Find all users associated with an application.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   Array of users.
   */
  public function findByApplication($appId) {
    $userRoles = [];
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $results = $userRoleMapper->findByAppId($appId);
    foreach ($results as $result) {
      $userRoles[] = $result->dump();
    }
    return $userRoles;
  }

  /**
   * Find a user by auth token.
   *
   * @param string $token
   *   Login token.
   *
   * @return array
   *   The user.
   */
  public function findByToken($token) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findBytoken($token);
    return $user->dump();
  }

  /**
   * Find a user by their user ID.
   *
   * @param string $uid
   *   User ID.
   *
   * @return array
   *   The user.
   */
  public function findByUid($uid) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUid($uid);
    return $user->dump();
  }

  /**
   * Find a user by their email.
   *
   * @param string $email
   *   User email.
   *
   * @return array
   *   The user.
   */
  public function findByEmail($email) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByEmail($email);
    return $user->dump();
  }

  /**
   * Find a user by their username.
   *
   * @param string $username
   *   User username.
   *
   * @return array
   *   The user.
   */
  public function findByUsername($username) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUsername($username);
    return $user->dump();
  }

}
