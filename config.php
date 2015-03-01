<?php

class Config
{
  /**
   * Add your server names to the appropriate arrays.
   */
  static private $_production = array('production');
  static private $_staging = array('api.naala.com.au');
  static private $_development = array('localhost', '127.0.0.1', 'datagator.local');

  static private $_allow_override;

  /**
   * database
   */
  static public $dbhost;
  static public $dbname;
  static public $dbuser;
  static public $dbpass;
  static public $dbpersistent;

  /**
   * cache
   */
  static public $cache;

  /**
   * directories
   */
  static public $dirRoot;
  static public $dirPublic;
  static public $dirIncludes;
  static public $dirApi;

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
  static public $swellnetUrl;
  static public $tokenLife;

  /**
   * setup the initial config
   * @param null $serverName
   */
  static public function load($serverName = NULL)
  {
    if (empty($serverName)) {
      $serverName = self::whereAmI($serverName);
    }

    self::everywhere();
    if (!$serverName) {
      die('Where am I? (You need to setup your server names in class.config.php) reported: ' . $serverName);
    } else {
      switch ($serverName) {
        case 'development':
          self::development();
          break;
        case 'staging':
          self::staging();
          break;
        case 'production':
          self::production();
          break;
      }
    }
    ini_set('error_log', self::$errorLog);
    self::override($serverName);
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
    if (in_array($serverName, self::$_production)) {
      return 'production';
    } elseif (in_array($serverName, self::$_staging)) {
      return 'staging';
    } elseif (in_array($serverName, self::$_development)) {
      return 'development';
    } else {
      return FALSE;
    }
  }

  /**
   * configurations for everywere
   */
  static private function everywhere()
  {
    self::$dirRoot = pathinfo(__FILE__, PATHINFO_DIRNAME) . '/';
    self::$dirPublic = self::$dirRoot . 'html/';
    self::$dirIncludes = self::$dirRoot . 'includes/';

    self::$defaultFormat = 'json';
    self::$swellnetUrl = 'http://swellnet.com.au';
    self::$tokenLife = '+1 day';
  }

  /**
   * configurations for development area
   */
  static private function development()
  {
    self::$debugDb = 0;
    self::$debug = 4;
    self::$debugCLI = 0;
    self::$_allow_override = TRUE;

    self::$debugInterface = 'HTML';
    self::$errorLog = '/var/log/apache2/swellnet_api.error.log';

    self::$cache = FALSE;

    self::$dbhost = 'localhost';
    self::$dbname = 'api_new';
    self::$dbuser = 'api_new';
    self::$dbpass = 'MyR9A4SdfgqcEzY8';
    self::$dbpersistent = FALSE;

    date_default_timezone_set('UTC');
    ini_set('error_reporting', E_ALL);
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', TRUE);
  }

  /**
   * configurations for staging area
   */
  static private function staging()
  {
    self::$debugDb = 0;
    self::$debug = 0;
    self::$debugCLI = 0;
    self::$_allow_override = TRUE;

    self::$debugInterface = 'LOG';
    self::$errorLog = '/var/log/apache2/swellnet_api.error.log';

    self::$cache = FALSE;

    self::$dbhost = 'localhost';
    self::$dbname = 'naalacom_api';
    self::$dbuser = 'naalacom_api';
    self::$dbpass = 'PmFNu1I_[48P';
    self::$dbpersistent = FALSE;

    ini_set('error_reporting', E_ALL);
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', FALSE);
  }

  /**
   * configurations for production area
   */
  static private function production()
  {
    self::$debugDb = 0;
    self::$debug = 0;
    self::$debugCLI = 0;
    self::$_allow_override = FALSE;

    self::$debugInterface = 'LOG';
    self::$errorLog = '/var/log/apache2/swellnet_api.error.log';

    self::$cache = TRUE;

    self::$dbhost = 'localhost';
    self::$dbname = 'swellnet_api';
    self::$dbuser = 'swellnet_api';
    self::$dbpass = 'MyR9A4SqcxjCEzY8';
    self::$dbpersistent = TRUE;

    ini_set('display_errors', FALSE);
  }

  /**
   * override configurations based on request vars
   * @param $serverName
   */
  static private function override($serverName)
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
