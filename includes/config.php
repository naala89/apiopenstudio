<?php

/**
 * Config for the entire API app.
 *
 * Important settings:
 *
 *   $_server: Assoc array of server name => fconfig function.
 *   This drives which specific config function to load.
 *
 *   everywhere(): settings that apply to all server areas.
 *   development(): settings that are intended to apply to all local dev machines.
 *   staging(): settings that are intended to apply to the staging server.
 *   production(): settings that are intended to apply to the production server.
 *
 * Individual settings:
 *
 *   $defaultFormat: the fallback output format if none is given in the header.
 *   $tokenLife: shelf life of an external token.
 *   $debug: general code debug levels.
 *     0=none
 *     1=Error
 *     2=Error & Warnings
 *     3=Error, Warnings, Info
 *     4=Verbose
 *   $debugCLI: CLI script debug level (levels as above).
 *   $debugDb: DB debug level (levels as above).
 *   $_allow_override: Allow override of config settttings in the URL -
 *     useful for dev areas. The name/val pairs in the URL are case sensitive,
 *     name should be the full config var without the "$"
 *   $debugInterface: where to output the debug parameters
 *     LOG - output all debug messages to the log
 *     HTML - output all debug messages to the resultant HTML output - this is only useful for dev areas.
 *   $cache: TRUE/FALSE to turn on the caching functionality
 *   DB (see http://adodb.sourceforge.net/ for details).
 *   $dbdriver: DSN DB driver (e.g. "mysqli").
 *   $dbhost: DB host.
 *   $dbname: DB name.
 *   $dbuser: DB username.
 *   $dbpass: DB password.
 *   $dboptions: extra config options for the DB.
 *   $errorLog: absolute path to the error log.
 *   $convert: absolute path to php module convert script (optional).
 *   $ffmpeg: absolute path to php module ffmpeg script (optional).
 */

namespace Datagator;

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
  static public $dirVendor;

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
   * setup the initial config
   * @param null $serverName
   */
  static public function load($serverName = NULL)
  {
    if (empty($serverName)) {
      $serverName = self::whereAmI($serverName);
    }
    if (!$serverName) {
      die('Where am I? You need to create server index in config::$_server ' . $serverName);
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
  static public function whereAmI($serverName = NULL)
  {
    if ($serverName === NULL) {
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
    self::$defaultFormat = 'json';
    self::$tokenLife = '+1 day';
    self::$dirVendor = $_SERVER["DOCUMENT_ROOT"] . '/vendor/';
  }

  /**
   * configurations for development area
   */
  static private function development()
  {
    self::$debug = 4;
    self::$debugCLI = 4;
    self::$_allow_override = TRUE;
    self::$debugInterface = 'HTML';

    self::$cache = FALSE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'datagator';
    self::$dbuser = 'datagator';
    self::$dbpass = 'datagator';
    self::$dboptions = array();
    self::$debugDb = FALSE;

    self::$errorLog = '/var/log/apache2/datagator.error.log';
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
    self::$dbname = 'datagator';
    self::$dbuser = '';
    self::$dbpass = '';
    self::$dboptions = array('persist' => 0);
    self::$debugDb = FALSE;

    self::$errorLog = '/home/apinaalacom/logs/datagator.error.log';
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
    self::$errorLog = '/var/log/apache2/datagator.error.log';

    self::$cache = TRUE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'datagator';
    self::$dbuser = '';
    self::$dbpass = '';
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
