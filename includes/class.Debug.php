<?php

class Debug
{

  private static $_level;
  private static $_interface;
  private static $_logFile;

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
   *  [optional] full logfile path an name. Default is '/var/log/apache/syslog'
   */
  public static function setup($_interface = self::HTML, $_level = 1, $_logFile = '/var/log/apache/syslog')
  {
    self::$_interface = $_interface;
    self::$_level = $_level;
    self::$_logFile = $_logFile;
    if ($_level > 0) {
      echo "<style type='text/css'>div.debug {background-color: #FFE7E7; border: solid #FF0000 1px;}</style>\n";
    }
  }

  /**
   * _display a debug message.
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
  public static function message($msg, $lvl = 1, $_level = NULL, $_interface = NULL)
  {
    if (!self::_shouldDebug($lvl, $_level)) {
      return;
    }
    if (empty($_interface)) {
      $_interface = self::$_interface;
    }
    if ($_interface == self::HTML) {
      $msg = htmlspecialchars($msg, ENT_QUOTES);
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
  public static function variable($var, $msg = 'DEBUG', $lvl = 1, $_level = NULL, $_interface = NULL)
  {
    if (!self::_shouldDebug($lvl, $_level)) {
      return;
    }
    if (empty($_interface)) {
      $_interface = self::$_interface;
    }
    if ($_interface == self::HTML) {
      $msg = '<b>' . htmlspecialchars($msg, ENT_QUOTES) . ':</b> ';
      if (is_array($var)) {
        $var = self::_htmlspecialchars_array($var);
      } elseif (is_string($var)) {
        $var = htmlspecialchars($var, ENT_QUOTES);
      }
    }
    self::_display($msg . (is_array($var) ? '<pre>'.print_r($var, TRUE).'</pre>' : print_r($var, TRUE)), $_interface);
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
  private static function _shouldDebug($lvl, $_level = NULL)
  {
    return $lvl <= (isset($_level) ? $_level : self::$_level);
  }

  /**
   * Utility function to display the debug text.
   *
   * @param str $str
   *  text to output
   * @param int $_interface
   *  output interface - uses const HTML & LOG
   */
  private static function _display($str, $_interface)
  {
    switch ($_interface) {
      case self::HTML:
        echo self::_beginLine($_interface) . $str . self::_endLine($_interface);
        break;
      case self::LOG:
      default:
        error_log(self::_beginLine($_interface) . $str . self::_endLine($_interface));
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
    switch ($_interface) {
      case self::HTML:
        $str = "<div class='debug'>";
        break;
      case self::LOG:
      default:
        $str = '';
        break;
    }
    return $str;
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
    switch ($_interface) {
      case self::HTML:
        $str = "</div>\n";
        break;
      case self::LOG:
      default:
        $str = '';
        break;
    }
    return $str;
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
      if (is_array($val))
        $str[$key] = self::_htmlspecialchars_array($val);
      elseif (is_resource($val))
        $str[$key] = $val;
      else
        $str[$key] = htmlspecialchars($val, ENT_QUOTES);
    }
    return $str;
  }
}
