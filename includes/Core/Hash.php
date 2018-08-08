<?php

/**
 *
 */

namespace Datagator\Core;

class Hash
{
  private static $iterations = 10000;

  /**
   * Generate a random salt string (default length 16 chars).
   *
   * @param int $length
   *
   * @return string
   */
  public static function generateSalt($length=16)
  {
    return mcrypt_create_iv($length);
  }

  /**
   * Generate a sha256 salted hash of a string.
   *
   * @param $string
   * @param $salt
   *
   * @return mixed|string
   */
  public static function generateHash($string, $salt)
  {
    return hash_pbkdf2('sha256', $string, $salt, self::$iterations, 32);
  }

  /**
   * Generate a unique random token, based on a string.
   *
   * @param int $string
   *
   * @return string
   */
  public static function generateToken($string)
  {
    return md5(time() . $string);
  }
}
