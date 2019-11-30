<?php

namespace Gaterdata\Core;

class Utilities
{

    public static $lower_case = 'abcdefghijklmnopqrstuvwxyz';
    public static $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public static $number = '0123456789';
    public static $special = '!@#$%^&*()';

  /**
   * Returns system time in micro secs.\
   *
   * @return float
   **/
    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

  /**
   * Creates a random string, of a specified length.
   *
   * Contents of string specified by $lower, $upper, $number and $non_alphanum.
   *
   * @param integer $length
   *  length of the string
   * @param boolean $lower
   *  include lower case alpha
   * @param boolean $upper
   *  include upper case alpha
   * @param boolean $number
   *  include integers
   * @param boolean $special
   *  include special characters
   *
   * @return string
   *  random string
   **/
    public static function randomString($length = 8, $lower = true, $upper = true, $number = true, $special = false)
    {
        $length = empty($length) ? 8 : $length;
        $lower = empty($lower) ? true : $lower;
        $upper = empty($upper) ? true : $upper;
        $number = empty($number) ? true : $number;
        $special = empty($special) ? false : $special;
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
   * Converts php date to standard mysql date
   *
   * @param date $phpdate
   *  date time stamp
   *
   * @return date
   *  mysql formatted datetime
   **/
    public static function datePhp2mysql($phpdate)
    {
        return date('Y-m-d H:i:s', $phpdate);
    }

  /**
   * Converts mysql date to standard php date.
   *
   * @param date $mysqldate
   *  mysql formatted datetime
   *
   * @return date
   *  php timestamp
   **/
    public static function dateMysql2php($mysqldate)
    {
        return strtotime($mysqldate);
    }

  /**
   * create current standard mysql date
   *
   * @return date
   *  mysql formatted datetime
   */
    public static function mysqlNow()
    {
        return self::datePhp2mysql(time());
    }

  /**
   * Check to see if $m_array is an associative array.
   *
   * @param mixed $m_array
   *  mixed array
   *
   * @return boolean
   *  is the array associative
   **/
    public static function isAssoc($m_array)
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
    public static function getUserIP()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $proxy = $_SERVER["HTTP_X_FORWARDED_FOR"];
        if (preg_match("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $proxy)) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return $ip;
    }

  /**
   * Get the current URL
   *
   * @param bool $array
   *  return in array format
   *
   * @return array|string
   *  current URL
   */
    public static function selfURL($array = false)
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
        $port = (($_SERVER["SERVER_PORT"] == 80) ? '' : ':' . $_SERVER["SERVER_PORT"]);
        $address = $_SERVER['SERVER_NAME'];
        $uri = $_SERVER['REQUEST_URI'];

        if (!$array) {
            return $protocol . '://' . $address . (($port == 80) ? '' : ":$port") . $uri;
        }
        $ret_array = array('protocol' => $protocol, 'port' => $port, 'address' => $address, 'uri' => $uri);
        return $ret_array;
    }

  /**
   * Return the character left of a substring win a string.
   *
   * @param string $s1
   *  string
   * @param string $s2
   *  substring
   *
   * @return string
   *  substring left of $s2
   */
    public static function strleft($s1, $s2)
    {
        return substr($s1, 0, strpos($s1, $s2));
    }

  /**
   * Redirect to current url under https, if under http.
   */
    public static function makeUrlSecure()
    {
        $a_selfURL = self::selfURL(true);
        if ($a_selfURL['protocol'] == 'http') {
            header('Location: ' . $a_selfURL['protocol']. 's://'
                . $a_selfURL['address'] . $a_selfURL['port'] . $a_selfURL['uri']);
            exit();
        }
    }

  /**
   * Redirect to current url under http, if under https.
   */
    public static function makeUrlInsecure()
    {
        $a_selfURL = self::selfURL(true);
        if ($a_selfURL['protocol'] == 'https') {
            header('Location: http://' . $a_selfURL['address'] . $a_selfURL['uri']);
            exit();
        }
    }

  /**
   * Check if a url exists.
   *
   * @param $url
   *  the URL
   *
   * @return bool
   *  does it exist
   */
    public static function doesUrlExist($url)
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
   * @return bool
   */
    public static function isSecure()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
            || !empty($_SERVER['HTTP_X_FORWARDED_SSL'])
            && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        return $isSecure;
    }

  /**
   * Recursively set access rights on a directory.
   *
   * @param $dir
   * @param int $dirAccess
   * @param int $fileAccess
   * @param array $nomask
   */
    public static function setAccessRights($dir, $dirAccess = 0777, $fileAccess = 0666, $nomask = array('.', '..'))
    {
      //error_log("Make writable: $dir");
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
}
