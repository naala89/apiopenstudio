<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Core;

class User
{
  private $dbSettings;
  private $db;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings)
  {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (sizeof($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof($this->dbSettings['options']) > 0 ? '?'.implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
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
   */
  public function adminLogin($accountName, $username, $password, $ttl)
  {
    $accountMapper = new Db\AccountMapper($this->db);
    $account = $accountMapper->findByName($accountName);
    if (empty($account->getAccId())) {
      return FALSE;
    }

    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUsername($username);
    if (empty($user->getUid())) {
      return FALSE;
    }

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $userRoles = $userRoleMapper->findByUidAccId($user->getUid(), $account->getAccId());
    if (empty($userRoles)) {
      return FALSE;
    }

    // set up salt if not defined
    if ($user->getSalt() == NULL) {
      $user->setSalt(Core\Hash::generateSalt());
    }

    // generate hash and compare to stored hash this prevents refreshing token with a fake password.
    $hash = Core\Hash::generateHash($password, $user->getSalt());
    if ($user->getHash() != null && $user->getHash() != $hash) {
      return FALSE;
    }

    // if token exists and is active, return it
    if (!empty($user->getToken())
      && !empty($user->getTokenTtl())
      && Core\Utilities::date_mysql2php($user->getTokenTtl()) > time()) {
      $user->setTokenTtl(Core\Utilities::date_php2mysql(strtotime($ttl)));
      return ['token' => $user->getToken(), 'accountName' => $account->getName(), 'accountId' => $account->getAccId()];
    }

    //perform login
    $user->setHash($hash);
    $token = Core\Hash::generateToken($username);
    $user->setToken($token);
    $user->setTokenTtl(Core\Utilities::date_php2mysql(strtotime($ttl)));
    $userMapper->save($user);

    return ['token' => $token, 'accountName' => $account->getName(), 'accountId' => $account->getAccId()];
  }

  /**
   * Create a user.
   *
   * @param $username
   * @param $password
   * @param null $email
   * @param null $honorific
   * @param null $nameFirst
   * @param null $nameLast
   * @param null $company
   * @param null $website
   * @param null $addressStreet
   * @param null $addressSuburb
   * @param null $addressCity
   * @param null $addressState
   * @param null $addressPostcode
   * @param null $phoneMobile
   * @param null $phoneWork
   *
   * @return bool|int
   */
  public function create($username, $password, $email=NULL, $honorific=NULL, $nameFirst=NULL, $nameLast=NULL, $company=NULL, $website=NULL, $addressStreet=NULL, $addressSuburb=NULL, $addressCity=NULL, $addressState=NULL, $addressPostcode=NULL, $phoneMobile=NULL, $phoneWork=NULL)
  {
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
   * Find all users associated with am account.
   *
   * @param int $accId
   *   Account ID.
   *
   * @return array
   *   Array of users.
   */
  public function findByAccount($accId) {
    $userRoles = [];
    $users = [];

    // Find account user roles.
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $results = $userRoleMapper->findByAccId($accId);
    foreach ($results as $result) {
      $userRoles += $this->findByApplication($result->getAppId());
    }
    $userRoles += $result->dump();

    // Find applications associated with the account.
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $applications = $applicationMapper->findByAccId($accId);
    // Find user roles associated with each application.
    foreach ($applications as $application) {
      $userRoles += $this->findByApplication($application->getAppId());
    }

    // Find users from $userRoles.
    $userMapper = new Db\UserMapper($this->db);
    foreach ($userRoles as $userRole) {
      $result = $userMapper->findByUid($userRole['uid']);
      $users += $result->dump();
    }

    return $users;
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
      $userRoles += $result->dump();
    }
    return $userRoles;
  }

  /**
   * Find user associated with a token.
   *
   * @param string $token
   *   $user login token.
   *
   * @return array
   */
  public function findByToken($token) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findBytoken($token);
    return $user->dump();
  }

}
