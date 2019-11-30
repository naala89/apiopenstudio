<?php

/**
 *
 */

namespace Gaterdata\Core;

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
   * Verify the password matches the hash.
   *
   * @param string $password
   * @param string hash
   *
   * @return bool
   */
    public function verifPassword($password, $hash)
    {
        return password_verify($password, $hash);
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
