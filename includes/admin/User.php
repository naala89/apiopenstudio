<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Config;
use GuzzleHttp;

Config::load();

class User
{
  public function __construct()
  {
    $_SESSION['token'] = '';
  }

  /**
   * Log a user in.
   *
   * @param $account
   * @param $username
   * @param $password
   *
   * @return bool
   */
  public function adminLogin($account, $username, $password)
  {
    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $accountMapper = new Db\AccountMapper($db);
    $account = $accountMapper->findByName($account);
    if (empty($account->getAccId())) {
      return FALSE;
    }

    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findByUsername($username);
    if (empty($user->getUid())) {
      return FALSE;
    }

    $userRoleMapper = new Db\UserRoleMapper($db);
    $userRoles = $userRoleMapper->findBy($user->getUid(), NULL, NULL, $account->getAccId());
    if (empty($userRoles)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Log a user out.
   *
   * @return bool
   */
  public function logout()
  {
    $_SESSION['token'] = '';
    return TRUE;
  }

  /**
   * Check if a user is logged in.
   *
   * @return bool
   */
  public function isLoggedIn()
  {
    return $_SESSION['token'] != '';
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

    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $db = \ADONewConnection($dsn);

    $userMapper = new Db\UserMapper($db);
    $result = $userMapper->save($user);
    if (!$result) {
      return FALSE;
    }
    $user = $userMapper->findByUsername($username);
    return $user->getUid();
  }
}