<?php

/**
 *
 */

namespace Datagator\Core;

class Hash
{
  private static $cost = 12;

  /**
   * Generate a sha256 salted hash of a string.
   *
   * @param $string
   *
   * @return mixed|string
   */
  public static function generateHash($string)
  {
    $options = [
      'cost' => self::$cost
    ];
    return password_hash($string, PASSWORD_BCRYPT, $options);
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
