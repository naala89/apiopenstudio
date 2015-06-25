<?php

class Config
{
  /**
   * Add your server names to the appropriate arrays.
   */
  static private $_server = array(
    'production' => 'production',
    'api.naala.com.au' => 'staging',
    'vmh17284.hosting24.com.au' => 'staging',
    'localhost' => 'development',
    '127.0.0.1' => 'development',
    'datagator.local' => 'development',
    'johns-MBP' => 'development'
  );
  static private $_allow_override;

  /**
   * database
   */
  static public $dbdriver;
  static public $dbhost;
  static public $dbname;
  static public $dbuser;
  static public $dbpass;
  static public $dboptions;

  /**
   * cache
   */
  static public $cache;

  /**
   * directories
   */
  static public $dirRoot;
  static public $dirPublic;
  static public $dirWams;
  static public $dirIncludes;

  /**
   * Command locations
   */
  static public $convert;
  static public $ffmpeg;

  /**
   * debug
   */
  static public $debug;
  static public $debugDb;
  static public $debugCLI;
  static public $debugInterface;
  static public $errorLog;

  /**
   * api
   */
  static public $defaultFormat;
  static public $tokenLife;

  /**
   * swellnet
   */
  static public $swellnetClientId;
  static public $weatherzoneForecastUri;
  static public $forecastRangeFull;
  static public $forecastRangeLimited;
  static public $swellnetWamsVideoSize;
  static public $swellnetWamsVideoDelay;
  static public $swellnetWamsVideoMorph;
  static public $swellnetWamsStandardImageFormat;

  /**
   * setup the initial config
   * @param null $serverName
   */
  static public function load($serverName = NULL)
  {
    if (empty($serverName)) {
      $serverName = self::whereAmI($serverName);
    }
    if (!$serverName || !method_exists('Config', $serverName)) {
      die('Where am I? (You need to setup your server names in class.config.php) reported: ' . $serverName);
    }

    self::everywhere();
    self::$serverName();
    self::override();
  }

  /**
   * return a string or false of the current staged area
   * @param null $serverName
   * @return bool|string
   */
  static public function whereAmI($serverName = null)
  {
    if ($serverName === null) {
      if (isset($_SERVER['SERVER_NAME'])) {
        $serverName = $_SERVER['SERVER_NAME'];
      } else {
        $serverName = system('echo $HOSTNAME');
      }
    }
    return self::$_server[$serverName];
  }

  /**
   * configurations for everywere
   */
  static private function everywhere()
  {
    self::$dirRoot = pathinfo(__FILE__, PATHINFO_DIRNAME) . '/';
    self::$dirPublic = self::$dirRoot . 'html/';
    self::$dirWams = self::$dirRoot . 'wams/';
    self::$dirIncludes = self::$dirRoot . 'includes/';

    self::$defaultFormat = 'json';
    self::$tokenLife = '+1 day';

    self::$swellnetClientId = 999;
    self::$weatherzoneForecastUri = 'api/v1/get-forecast/%locId%/latest/?verbose=1&currentday=1';
    self::$forecastRangeFull = 16;
    self::$forecastRangeLimited = 5;
    self::$swellnetWamsVideoSize = '200x200';
    self::$swellnetWamsVideoDelay = '10';
    self::$swellnetWamsVideoMorph = '10';
    self::$swellnetWamsStandardImageFormat = 'png';
  }

  /**
   * configurations for development area
   */
  static private function development()
  {
    self::$debug = 4;
    self::$debugCLI = 4;
    self::$_allow_override = TRUE;
    self::$debugInterface = 'LOG';

    self::$cache = FALSE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'swellnet_api';
    self::$dbuser = 'swellnet_api';
    self::$dbpass = 'MyR9A4SdfgqcEzY8';
    self::$dboptions = array();
    self::$debugDb = FALSE;

    self::$errorLog = '/var/log/apache2/swellnet_api-error.log';
    self::$convert = '/usr/local/bin/convert';
    self::$ffmpeg = '/usr/local/bin/ffmpeg';
    date_default_timezone_set('Australia/Sydney');

    ini_set('display_errors', 'on');
    ini_set('log_errors','On');
    ini_set('error_reporting', E_ALL);
    ini_set('error_log', self::$errorLog);
  }

  /**
   * configurations for staging area
   */
  static private function staging()
  {
    self::$debug = 1;
    self::$debugCLI = 1;
    self::$_allow_override = TRUE;
    self::$debugInterface = 'LOG';

    self::$cache = FALSE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'apinaala_api';
    self::$dbuser = 'apinaala_api';
    self::$dbpass = '_DN2~o-s';
    self::$dboptions = array('persist' => 0);
    self::$debugDb = FALSE;

    self::$errorLog = '/home/apinaalacom/logs/api-error.log';
    self::$convert = '/usr/bin/convert';
    self::$ffmpeg = '/usr/bin/ffmpeg';
    date_default_timezone_set('UTC');

    ini_set('display_errors', 'on');
    ini_set('log_errors','On');
    ini_set('error_reporting', E_ALL);
    ini_set('error_log', self::$errorLog);
  }

  /**
   * configurations for production area
   */
  static private function production()
  {
    self::$debug = 0;
    self::$debugCLI = 0;
    self::$_allow_override = FALSE;
    self::$debugInterface = 'LOG';
    self::$errorLog = '/var/log/apache2/swellnet_api.error.log';

    self::$cache = TRUE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'swellnet_api';
    self::$dbuser = 'swellnet_api';
    self::$dbpass = 'MyR9A4SqcxjCEzY8';
    self::$dboptions = array('persist' => 0);
    self::$debugDb = FALSE;

    ini_set('display_errors', FALSE);
  }

  /**
   * Override configurations based on request vars.
   */
  static private function override()
  {
    if (!self::$_allow_override) {
      return;
    }
    $get = $_GET;
    foreach ($get as $k => $v) {
      if (property_exists('Config', $k)) {
        self::$$k = $v;
      }
    }
  }
}

Config::load();
