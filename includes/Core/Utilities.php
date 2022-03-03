<?php

/**
 * Class Utilities.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use DateInterval;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

/**
 * Class Utilities
 *
 * Global utilities.
 */
class Utilities
{
    /**
     * String of lower case letters for random().
     *
     * @var string Lower case characters.
     */
    public static string $lower_case = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * String of capital letters for random().
     *
     * @var string Upper case characters.
     */
    public static string $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * String of numbers for random().
     *
     * @var string Real numbers.
     */
    public static string $number = '0123456789';

    /**
     * String of special characters for random().
     *
     * @var string Special characters.
     */
    public static string $special = '!@#$%^&*()';

    /**
     * Returns system time in micro secs.
     *
     * @return float
     **/
    public static function getMicrotime(): float
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * Creates a random string, of a specified length.
     *
     * Contents of string specified by $lower, $upper, $number and $non_alphanum.
     *
     * @param int|null $length Length of the string.
     * @param boolean|null $lower Include lower case alpha.
     * @param boolean|null $upper Include upper case alpha.
     * @param boolean|null $number Include integers.
     * @param boolean|null $special Include special characters.
     *
     * @return string
     */
    public static function randomString(
        int $length = null,
        bool $lower = null,
        bool $upper = null,
        bool $number = null,
        bool $special = null
    ): string {
        $length = empty($length) ? 8 : $length;
        $lower = $lower === null || $lower;
        $upper = $upper === null || $upper;
        $number = $number === null || $number;
        $special = !($special === null) && $special;
        $chars = '';
        if ($lower) {
            $chars .= self::$lower_case;
        }
        if ($upper) {
            $chars .= self::$upper_case;
        }
        if ($number) {
            $chars .= self::$number;
        }
        if ($special) {
            $chars .= self::$special;
        }

        $str = '';
        $count = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $count - 1)];
        }

        return $str;
    }

    /**
     * Converts php date to standard mysql date.
     *
     * @param integer $phpdate Unix time stamp.
     *
     * @return string
     **/
    public static function datePhp2mysql(int $phpdate): string
    {
        return date('Y-m-d H:i:s', $phpdate);
    }

    /**
     * Returns the current OpenApiPath* classname
     *
     * @param Config $settings
     *
     * @return string
     *
     * @throws ApiException
     */
    public static function getOpenApiPathClassPath(Config $settings): string
    {
        return "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiPath" .
            str_replace('.', '', $settings->__get(['api', 'openapi_version']));
    }

    /**
     * Returns the current OpenApiParent* classname
     *
     * @param Config $settings
     *
     * @return string
     *
     * @throws ApiException
     */
    public static function getOpenApiParentClassPath(Config $settings): string
    {
        return "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiParent" .
            str_replace('.', '', $settings->__get(['api', 'openapi_version']));
    }

    /**
     * Converts mysql date to standard php date.
     *
     * @param integer $mysqldate Unix time stamp.
     *
     * @return string
     **/
    public static function dateMysql2php(int $mysqldate): string
    {
        return strtotime($mysqldate);
    }

    /**
     * Create current standard mysql date
     *
     * @return string
     */
    public static function mysqlNow(): string
    {
        return self::datePhp2mysql(time());
    }

    /**
     * Check to see if $m_array is an associative array.
     *
     * @param mixed $m_array Mixed array.
     *
     * @return boolean
     **/
    public static function isAssoc($m_array): bool
    {
        if (!is_array($m_array)) {
            return false;
        }
        return array_keys($m_array) !== range(0, count($m_array) - 1);
    }

    /**
     * Obtain user IP even if they're under a proxy.
     *
     * @return string ip address
     *  IP address of the user
     */
    public static function getUserIp(): string
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $proxy = $_SERVER["HTTP_X_FORWARDED_FOR"];
        if (preg_match("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $proxy)) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return $ip;
    }

    /**
     * Get the current URL.
     *
     * @param boolean $returnArray Return in array format.
     *
     * @return array|string
     */
    public static function selfUrl(bool $returnArray = null)
    {
        $s = (empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on")) ? "s" : "";
        $protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
        $port = (($_SERVER["SERVER_PORT"] == 80) ? '' : ':' . $_SERVER["SERVER_PORT"]);
        $address = $_SERVER['SERVER_NAME'];
        $uri = $_SERVER['REQUEST_URI'];

        if (!$returnArray) {
            return $protocol . '://' . $address . (($port == 80) ? '' : ":$port") . $uri;
        }
        return array('protocol' => $protocol, 'port' => $port, 'address' => $address, 'uri' => $uri);
    }

    /**
     * Return the character left of a substring win a string.
     *
     * @param string $s1 String.
     * @param string $s2 Substring.
     *
     * @return string substring left of $s2
     */
    public static function strleft(string $s1, string $s2): string
    {
        return substr($s1, 0, strpos($s1, $s2));
    }

    /**
     * Redirect to current url under https, if under http.
     *
     * @return void
     */
    public static function makeUrlSecure()
    {
        $a_selfUrl = self::selfUrl(true);
        if ($a_selfUrl['protocol'] == 'http') {
            header('Location: ' . $a_selfUrl['protocol'] . 's://'
                . $a_selfUrl['address'] . $a_selfUrl['port'] . $a_selfUrl['uri']);
            exit();
        }
    }

    /**
     * Redirect to current url under http, if under https.
     *
     * @return void
     */
    public static function makeUrlInsecure()
    {
        $a_selfUrl = self::selfUrl(true);
        if ($a_selfUrl['protocol'] == 'https') {
            header('Location: http://' . $a_selfUrl['address'] . $a_selfUrl['uri']);
            exit();
        }
    }

    /**
     * Check if a url exists.
     *
     * @param string $url The URL.
     *
     * @return boolean
     */
    public static function doesUrlExist(string $url): bool
    {
        $headers = @get_headers($url);
        if (strpos($headers[0], '200') === false) {
            return false;
        }
        return true;
    }

    /**
     * Check if current url is https.
     *
     * @return boolean
     */
    public static function isSecure(): bool
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        } elseif (
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
            || !empty($_SERVER['HTTP_X_FORWARDED_SSL'])
            && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'
        ) {
            $isSecure = true;
        }
        return $isSecure;
    }

    /**
     * Recursively set access rights on a directory.
     *
     * @param string $dir Directory string.
     * @param int|null $dirAccess Directory permission to set.
     * @param int|null $fileAccess File permission to set.
     * @param array $nomask Nomask permission to set.
     *
     * @return void
     */
    public static function setAccessRights(
        string $dir,
        int $dirAccess = null,
        int $fileAccess = null,
        array $nomask = array('.', '..')
    ) {
        $dirAccess = empty($dirAccess) ? 0777 : $dirAccess;
        $fileAccess = empty($dirAccess) ? 0666 : $fileAccess;
        if (is_dir($dir)) {
            // Try to make each directory world writable.
            if (@chmod($dir, $dirAccess)) {
                error_log("Make writable: $dir");
            }
        }
        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $nomask) && $file[0] != '.') {
                    if (is_dir("$dir/$file")) {
                        // Recurse into subdirectories
                        self::setAccessRights("$dir/$file", $dirAccess, $fileAccess, $nomask);
                    } else {
                        $filename = "$dir/$file";
                        // Try to make each file world writable.
                        if (@chmod($filename, $fileAccess)) {
                            error_log("Make writable: $filename");
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * Get Authorization header bearer token.
     *
     * @return mixed|string|null
     */
    public static function getAuthHeaderToken()
    {
        $headers = '';

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Nginx or fast CGI.
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about
            // capitalization for Authorization)
            $requestHeaders = array_combine(
                array_map(
                    'ucwords',
                    array_keys($requestHeaders)
                ),
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        $headerParts = explode(' ', $headers);
        return array_pop($headerParts);
    }

    /**
     * Decrypt and validate the JWT token.
     *
     * @param string|null $rawToken
     *
     * @return UnencryptedToken
     *
     * @throws ApiException
     */
    public static function decryptToken(string $rawToken = null): UnencryptedToken
    {
        $config = new Config();
        if (empty($rawToken)) {
            $rawToken = self::getAuthHeaderToken();
        }

        $algorithm =
            "Lcobucci\\JWT\\Signer\\" .
            $config->__get(['api', 'jwt_alg_type']) .
            "\\" .
            $config->__get(['api', 'jwt_alg']);
        $jwtConfig = Configuration::forAsymmetricSigner(
            new $algorithm(),
            LocalFileReference::file($config->__get(['api', 'jwt_private_key'])),
            LocalFileReference::file($config->__get(['api', 'jwt_public_key']))
        );
        $clock = new SystemClock(new DateTimeZone(date('T')));
        $leeway = new DateInterval('PT60S');
        $jwtConfig->setValidationConstraints(
            new IssuedBy($config->__get(['api', 'jwt_issuer'])),
            new PermittedFor($config->__get(['api', 'jwt_permitted_for'])),
            new LooseValidAt($clock, $leeway)
        );
        $constraints = $jwtConfig->validationConstraints();

        $decryptedToken = $jwtConfig->parser()->parse($rawToken);
        if (!assert($decryptedToken instanceof UnencryptedToken)) {
            throw new ApiException('invalid token', 4, -1, 401);
        }

        try {
            $jwtConfig->validator()->assert($decryptedToken, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            throw new ApiException('invalid token', 4, -1, 401);
        }

        return $decryptedToken;
    }

    /**
     * Get the User ID from the JWT token.
     *
     * @param UnencryptedToken|null $decryptedToken Decrypted JWT token.
     *
     * @return int
     *
     * @throws ApiException
     */
    public static function getUidFromToken(UnencryptedToken $decryptedToken = null): int
    {
        if (empty($decryptedToken)) {
            $decryptedToken = self::decryptToken();
        }
        try {
            $uid = $decryptedToken->claims()->get('uid');
            if (!assert(!empty($uid))) {
                throw new ApiException('user ID not included in the claim', 4, -1, 401);
            }
        } catch (RequiredConstraintsViolated $e) {
            throw new ApiException('Invalid token', 4, -1, 401);
        }

        return $uid;
    }

    /**
     * Get the User roles from the JWT token.
     *
     * @param UnencryptedToken|null $decryptedToken Decrypted JWT token.
     *
     * @return array
     *
     * @throws ApiException
     */
    public static function getRolesFromToken(UnencryptedToken $decryptedToken = null): array
    {
        if (empty($decryptedToken)) {
            $decryptedToken = self::decryptToken();
        }
        try {
            $roles = $decryptedToken->claims()->get('roles');
            if (!assert(!empty($roles))) {
                throw new ApiException('user roles not included in the claim', 4, -1, 401);
            }
        } catch (RequiredConstraintsViolated $e) {
            throw new ApiException('Invalid token', 4, -1, 401);
        }

        return $roles;
    }
}
