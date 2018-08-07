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
   * @param $username
   * @param $password
   *
   * @return bool|mixed
   */
  public function login($username, $password)
  {
    $payload = ['form_params' => [
      'username' => $username,
      'password' => $password
    ]];
    $url = Config::$domainName . '/api/user/login';
    $client = new GuzzleHttp\Client();
    try {
      $result = json_decode($client->post($url, $payload));
    } catch (\Exception $e) {
      var_dump($e->getMessage());exit;
      return FALSE;
    }
    if (is_array($result) && isset($result['token'])) {
      $_SESSION['token'] = $result['token'];
      return $result;
    }
    return FALSE;
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