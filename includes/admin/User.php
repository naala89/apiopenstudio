<?php

namespace Datagator\Admin;

use Datagator\Config;
use GuzzleHttp;
use Datagator\Db;

class User
{
  public function __construct()
  {
    $_SESSION['token'] = '';
  }

  public function login($username, $password)
  {
    $payload = ['form_params' => [
      'username' => $username,
      'password' => $password
    ]];
    $url = '/api/user/login';
    $client = new GuzzleHttp\Client();
    try {
      $result = json_decode($client->request('POST', $url, $payload));
    } catch (\Exception $e) {
      return FALSE;
    }
    if (is_array($result) && isset($result['token'])) {
      $_SESSION['token'] = $result['token'];
      return $result;
    }
    return FALSE;
  }

  public function logout()
  {
    $_SESSION['token'] = '';
    return TRUE;
  }

  public function isLoggedIn()
  {
    return $_SESSION['token'] != '';
  }

  public function create($username=NULL, $password=NULL, $email=NULL, $honorific=NULL, $nameFirst=NULL, $nameLast=NULL, $company=NULL, $website=NULL, $addressStreet=NULL, $addressSuburb=NULL, $addressCity=NULL, $addressState=NULL, $addressPostcode=NULL, $phoneMobile=NULL, $phoneWork=NULL)
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
    $uid = $user->getUid();

    $roleMapper = new Db\RoleMapper($db);
    $role = $roleMapper->findByName('Owner');
    $rid = $role->getRid();

    $userRole = new Db\UserRole(NULL, $uid, $rid);
    $userRoleMapper = new Db\UserRoleMapper($db);
    $result = $userRoleMapper->save($userRole);
    if (!$result) {
      return FALSE;
    }

    return TRUE;
  }
}