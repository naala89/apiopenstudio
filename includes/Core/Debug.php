<?php

namespace Datagator\Core;

class Debug
{
  private static $_level;
  private static $_interface;
  private static $_logFile; // for some reason, error_log always goes to error_log

  const HTML = 1;
  const LOG = 2;

  /**
   * Set debug level.
   *
   * @param int $_level
   *  debug level
   */
  public static function setLevel($_level)
  {
    self::$_level = $_level;
  }

  /**
   * Get debug level.
   *
   * @return int $_level
   *  debug level
   */
  public static function getLevel()
  {
    return self::$_level;
  }

  /**
   * Set where the debug output is to go.
   *
   * @param int $_interface
   *  uses const HTML or LOG
   */
  public static function setInterface($_interface)
  {
    self::$_interface = $_interface;
  }

  /**
   * Get where the debug output is going.
   *
   * @return int $_interface
   *  uses const HTML or LOG
   */
  public static function getInterface()
  {
    return self::$_interface;
  }

  /**
   * Set logfile.
   *
   * If using LOG output interface, it is possivble to set a different log than that set by apache.
   *
   * @param string $_logFile
   *  full logfile path an name
   */
  public static function setLogFile($_logFile)
  {
    self::$_logFile = $_logFile;
  }

  /**
   * Get logfile.
   *
   * @return string $_logFile
   *  full logfile path an name
   */
  public static function getLogFile()
  {
    return self::$_logFile;
  }

  /**
   * Initial setup of the debug class.
   *
   * This should always be called after instantiation, using config vars.
   *
   * @param int $_interface
   *  [optional]  uses const HTML or LOG. default is HTML
   * @param int $_level
   *  [optional] debug level. Default is 1
   * @param string $_logFile
   *  [optional] full logfile path an name. Default is '' (which will go to default system logfile)
   */
  public static function setup($_interface=self::HTML, $_level=1, $_logFile='')
  {
    self::$_interface = $_interface;
    self::$_level = $_level;
    self::$_logFile = $_logFile;
    if (self::$_level > 0 && self::$_interface == self::HTML) {
      echo "<style type='text/css'>div.debug {background-color: #FFE7E7; border: solid #FF0000 1px;}</style>\n";
    }
  }

  /**
   * Display a debug message.
   *
   * The default debug level can be overridden for separate debug, like DB.
   *
   * @param string $msg
   *  the debug message
   * @param int $lvl
   *  [optional] the atomic debug level of the message. Default is 1.
   * @param null $_level
   *  [optional] debug level to compare against. NULL indicates to use stored debug level.
   * @param null $_interface
   *  [optional] where the debug output is to go. NULL indicates to use stored output interface.
   */
  public static function message($msg, $lvl=1, $_level=NULL, $_interface=NULL)
  {
    if (!self::_shouldDebug($lvl, $_level)) {
      return;
    }
    $_interface = is_null($_interface) ? self::$_interface : $_interface;
    if ($_interface == self::HTML) {
      $msg = htmlspecialchars($msg, ENT_QUOTES);
    } else {
      $msg = self::_timestampString() . "$msg\n";
    }
    self::_display($msg, $_interface);
  }

  /**
   * Display variable with a message.
   *
   * The default debug level can be overridden for separate debug, like DB.
   *
   * @param $var
   *  variable to display in debug.
   * @param string $msg
   *  [optional] message to precede the variable in the debug
   * @param int $lvl
   *  [optional] the atomic debug level of the message. Default is 1.
   * @param null $_level
   *  [optional] debug level to compare against. NULL indicates to use stored debug level.
   * @param null $_interface
   *  [optional] where the debug output is to go. NULL indicates to use stored output interface.
   */
  public static function variable($var, $msg='DEBUG', $lvl=1, $_level=NULL, $_interface=NULL)
  {
    if (!self::_shouldDebug($lvl, $_level)) {
      return;
    }

    $_interface = is_null($_interface) ? self::$_interface : $_interface;

    if ($_interface == self::HTML) {
      $msg = '<b>' . htmlspecialchars($msg, ENT_QUOTES) . ':</b>';
      if (is_array($var) || is_object($var)) {
        //$var = self::_htmlspecialchars_array($var);
      } else {
        $var = htmlspecialchars($var, ENT_QUOTES);
      }
      $msg .= (is_array($var) || is_object($var) ? "<pre>\n" . print_r($var, true) . '</pre>' : print_r($var, true));
    } else {
      $msg = self::_timestampString() . $msg . ': ' . print_r($var, true);
      $msg .= !is_array($var) && !is_object($var) ? "\n" : '';
    }

    self::_display($msg, $_interface);
  }

  /**
   * get standard logging timestamp formatted string
   *
   * @return string
   */
  private static function _timestampString()
  {
    $date = new \DateTime('now');
    return '[' . $date->format('D M d h:i:s') . substr((string)microtime(), 1, 6) . $date->format(' Y') . '] ';
  }

  /**
   * Utility function to decide whether to display debug message
   *
   * @param int $lvl
   *  atomic debug level of the message
   * @param int $_level
   *  debug level to compare against. NULL indicates to use stored debug level
   *
   * @return bool
   */
  private static function _shouldDebug($lvl, $_level=NULL)
  {
    return $lvl <= (is_null($_level) ? self::$_level : $_level);
  }

  /**
   * Utility function to display the debug text.
   *
   * @param str $str
   *  text to output
   * @param int $_interface
   *  output interface - uses const HTML & LOG
   *
   * @TODO: The problem of not writing to specified logs appears to be here. the commented out code does not write to $_logfile, regardless.
   */
  private static function _display($str, $_interface)
  {
    switch ($_interface) {
      case self::HTML:
        echo self::_beginLine($_interface) . $str . self::_endLine($_interface);
        break;
      case self::LOG:
      default:
        if (empty(self::$_logFile)) {
          error_log(self::_beginLine($_interface) . $str . self::_endLine($_interface));
        } else {
          error_log(self::_beginLine($_interface) . $str . self::_endLine($_interface), 3, self::$_logFile);
        }
        break;
    }
  }

  /**
   * Get the debug prefix text.
   *
   * This is used to define prefix text, depending on the output interface. e.g. <div> for HTML
   *
   * @param int $_interface
   *  output interface - uses const HTML & LOG
   *
   * @return str $str
   */
  private static function _beginLine($_interface)
  {
    return $_interface === self::HTML ? "<div class='debug'>" : '';
  }

  /**
   * Get the debug suffix text.
   *
   * This is used to define suffix text, depending on the output interface. e.g. </div> for HTML
   *
   * @param int $_interface
   *  output interface - uses const HTML & LOG
   *
   * @return str $str
   */
  private static function _endLine($_interface)
  {
    return $_interface === self::HTML ? "</div>\n" : '';
  }

  /**
   * Recursive utility function to convert variable to display safe characters.
   *
   * Accepts arrays and normal variables.
   *
   * @param mixed $arr
   *  the var to make safe.
   *
   * @return mixed $str
   */
  private static function _htmlspecialchars_array($arr)
  {
    $str = array();
    while (list($key, $val) = each($arr)) {
      if (is_array($val) || is_object($val))
        $str[$key] = self::_htmlspecialchars_array($val);
      elseif (is_resource($val))
        $str[$key] = $val;
      else
        $str[$key] = htmlspecialchars($val, ENT_QUOTES);
    }
    return $str;
  }
}
