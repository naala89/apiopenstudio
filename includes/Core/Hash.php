<?php

/**
 * Class Hash.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

/**
 * Class Hash
 *
 * Generate password and hash functions.
 */
class Hash
{
    /**
     * Iterations for hash.
     *
     * @var integer Password hash cost.
     */
    private static int $cost = 12;

  /**
   * Generate a sha256 salted hash of a string.
   *
   * @param string $string String to hash.
   *
   * @return string
   */
    public static function generateHash(string $string): string
    {
        $options = [
            'cost' => self::$cost
        ];
        return password_hash($string, PASSWORD_BCRYPT, $options);
    }

  /**
   * Verify the password matches the hash.
   *
   * @param string $password Password.
   * @param string $hash Hash.
   *
   * @return boolean
   */
    public static function verifPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

  /**
   * Generate a unique random token, based on a string.
   *
   * @param string $string String to create a token from.
   *
   * @return string
   */
    public static function generateToken(string $string): string
    {
        return md5(time() . $string);
    }
}
