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
    'datagator.yourtemp.website' => 'staging',
    'localhost' => 'development',
    '127.0.0.1' => 'development',
    'datagator.local' => 'development',
    'johns-MBP' => 'development',
    'johns-MBP-2' => 'staging'
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
  static public $dirYaml;
  static public $dirUploads;

  /**
   * email
   */
  static public $emailService;
  static public $emailHost;
  static public $emailAuth;
  static public $emailUser;
  static public $emailPass;
  static public $emailSecure;
  static public $emailPort;

  /**
   * setup the initial config
   * @param null $serverName
   */
  static public function load($serverName = NULL)
  {
    $environment = self::whereAmI($serverName);

    if (!$environment) {
      die('Where am I? You need to create server index in config::$_server ' . $environment);
    }

    self::everywhere();
    self::$environment();
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
    self::$tokenLife = '+1 hour';
    self::$dirYaml = '/resources/';
    self::$dirUploads = '/uploads/';
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
    self::$tokenLife = '+1 day';

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'datagator';
    self::$dbuser = 'datagator';
    self::$dbpass = 'datagator';
    self::$dboptions = array();
    self::$debugDb = FALSE;

    self::$emailService = 'mail'; //'qmail', 'sendmail', 'smtp', 'mail'
    self::$emailHost = '';
    self::$emailAuth = false;
    self::$emailUser = '';
    self::$emailPass = '';
    self::$emailSecure = 'tls'; // tls or ssh
    self::$emailPort = 587;

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
    self::$tokenLife = '+1 day';

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'datagato_api';
    self::$dbuser = 'datagato_datagat';
    self::$dbpass = 'V0Y_CIiY';
    self::$dboptions = array('persist' => 0);
    self::$debugDb = FALSE;

    self::$emailService = 'mail'; //'qmail', 'sendmail', 'smtp', 'mail'
    self::$emailHost = '';
    self::$emailAuth = false;
    self::$emailUser = '';
    self::$emailPass = '';
    self::$emailSecure = 'tls'; // tls or ssh
    self::$emailPort = 587;

    self::$errorLog = '/home/datagator/datagator.error.log';
    date_default_timezone_set('Australia/Sydney');
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

    self::$cache = TRUE;

    self::$dbdriver = 'mysqli';
    self::$dbhost = 'localhost';
    self::$dbname = 'datagator';
    self::$dbuser = '';
    self::$dbpass = '';
    self::$dboptions = array('persist' => 0);
    self::$debugDb = FALSE;

    self::$emailService = 'mail'; //'qmail', 'sendmail', 'smtp', 'mail'
    self::$emailHost = '';
    self::$emailAuth = false;
    self::$emailUser = '';
    self::$emailPass = '';
    self::$emailSecure = 'tls'; // tls or ssh
    self::$emailPort = 587;

    self::$errorLog = '/var/log/apache2/datagator.error.log';
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
      if (property_exists("\\Datagator\\Config", $k)) {
        self::$$k = urldecode($v);
      }
    }
  }
}
