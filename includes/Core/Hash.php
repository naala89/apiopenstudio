<?php

/**
 *
 */

namespace Datagator\Core;

class Hash
{
  private static $iterations = 10000;

  /**
   * @param $password
   * @param null $salt
   * @return mixed|string
   */
  public static function generateHash($password, $salt=NULL)
  {
    $salt = empty($salt) ? self::generateSalt() : $salt;
    return hash_pbkdf2('sha256', $password, $salt, self::$iterations, 32);
  }

  /**
   * @param int $length
   * @return string
   */
  public static function generateSalt($length=16)
  {
    return mcrypt_create_iv($length);
  }
}
