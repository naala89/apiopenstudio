<?php

/**
 * Provide username/ Password authentication
 *
 * this class is to be used by ProcessorInput.
 * The map parameters allow user/pass var names to be mapped to different get vars
 *
 * Meta:
 *    {
 *      "type": "userpass",
 *      "map": {
 *        "username": <string>,
 *        "password": <string>
 *      }
 *    }
 */
class AuthUserpass
{
  private $username = '';
  private $password = '';

  public function AuthUserpass($map, $params)
  {
    Debug::variable($map, 'map');
    Debug::variable($params, 'params');
    $username = 'username';
    $password = 'password';
    if (!empty($map)) {
      $username = $map['username'];
      $password = $map['password'];
    }

    if (isset($params[$username])) {
      $this->username = $params[$username];
    }
    if (isset($params[$password])) {
      $this->password = $params[$password];
    }
  }

  public function process()
  {
    $arr = array(
      CURLOPT_USERPWD => $this->username . ':' . $this->password
    );
    Debug::variable($arr, 'auth');
    return $arr;
  }
}