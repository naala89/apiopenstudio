#!/usr/bin/php -q
<?php
/**
 * Class CLIScript
 *
 * This is the base class for an asynchronous solution within PHP.
 *
 * The main code generates a command string in the form of
 * "php script.php -option1 value1 --flag1 --flag2 argument1 argument2"
 *
 * script.php instantiates the child class of CLIScript, and then calls child_class->exec();
 *
 * exec arguments are stored as:
 * $this->exec script being called
 * $this->options argv -options as an associated array of $this->options => val
 * $this->flags argv --flag
 * $this->arguments argv argument
 *
 * $argmap defines the options, flags and arguments
 * $argMap['options']['required'] bool option required y/n
 * $argMap['options']['multiple'] bool option can have more than 1 value y/n
 * $argMap['options']['permittedValues'] array permitted option values
 * $argMap['options']['default'] default value
 *
 * The use of default value is for params that are not required in the script call, but actually need a value.
 * i.e. default value.
 */

namespace Gaterdata\Core\Cli;

use Gaterdata\Core\Config;
use Monolog\Logger;

abstract class CLIScript
{
    protected $exec;
    protected $options;
    protected $flags;
    protected $arguments;
    protected $argMap;

    /**
     * constructor
     */
    public function __construct()
    {
    }

    /**
     * load arguments for the function
     * @param $argv
     * @return void
     */
    public function load($argv)
    {
        if (isset($argv[1]) && ($argv[1] == '--help' || $argv[1] == '-h')) {
            $this->help();
            exit();
        }
        $this->_getArgs($argv);
        $this->_validateOptions();
        $this->_validateFlags();
    }

    /**
     * execute the function
     * @param void
     * @return void
     */
    public function exec($argv = null)
    {
        if (!empty($argv)) {
            $this->load($argv);
        }
    }

    /**
     * abstract function help
     * @param void
     * @return void
     */
    protected function help()
    {
        echo "There is no help defined.\n";
    }

    /**
     * helper function to die
     * @param string $msg log message
     * @return void
     */
    protected function timeToDie($msg)
    {
        if (is_array($msg)) {
            foreach ($msg as $m) {
                Debug::message($m, 1, Config::$debugCLI, Debug::LOG);
            }
        } else {
            Debug::message($msg, 1, Config::$debugCLI, Debug::LOG);
        }
        die("Error: controlled termination of cli script.\n");
    }

    /**
     * validate supplied options
     * @param void
     * @return void
     */
    private function _validateOptions()
    {
        $this->_validateRequired($this->options, 'options');
        foreach ($this->options as $name => $value) {
            $this->_validateAllowed($name, 'options');
            $this->_validateMultiple($name, $value, 'options');
            if (isset($this->argMap['options'][$name]['permittedValues'])) {
                $this->_validatePermitted($name, $value, 'options');
            }
        }
    }

    /**
     * validate supplied flags
     * @param void
     * @return void
     */
    private function _validateFlags()
    {
        Debug::variable($this->flags, 'flags', 1, Config::$debugCLI, Debug::LOG);
        foreach ($this->flags as $name => $value) {
            Debug::variable($this->argMap['flags'], '$this->argMap[$index]', 1, Config::$debugCLI, Debug::LOG);
            $this->_validateAllowed($name, 'flags');
        }
    }

    /**
     * validate all required options are present
     * @param $opt
     * @param $index
     * @return void
     */
    private function _validateRequired($opt, $index)
    {
        $error = false;
        $messages = array();
        foreach ($this->argMap[$index] as $name => $value) {
            if ($value['required'] && !isset($opt[$name])) {
                $messages[] = 'ERROR: required argv ' . $index . ' "' . $name . "\" not present\n";
                $error = true;
            }
        }
        if ($error) {
            $this->timeToDie($messages);
        }
    }

    /**
     * validate all supplied arguments are allowed
     * @param $name
     * @param $index
     * @return void
     */
    private function _validateAllowed($name, $index)
    {
        $error = false;
        $messages = array();
        if (!isset($this->argMap[$index][$name])) {
            $messages[] = 'ERROR: argv ' . $index . ' "' . $name . "\" not allowed";
            $error = true;
        }
        if ($error) {
            $this->timeToDie($messages);
        }
    }

    /**
     * validate any supplied arguments with multiple values are allowed
     * @param $name
     * @param $value
     * @param $index
     * @return void
     */
    private function _validateMultiple($name, $value, $index)
    {
        $error = false;
        $messages = array();
        if (!$this->argMap[$index][$name]['multiple'] && is_array($value)) {
            $messages[] = 'ERROR: argv "' . $name . "\" not allowed multiple values";
            $error = true;
        }
        if ($error) {
            $this->timeToDie($messages);
        }
    }

    /**
     * validate against any permitted value restrictions
     * @param $name
     * @param $value
     * @param $index
     * @return void
     */
    private function _validatePermitted($name, $value, $index)
    {
        $error = false;
        $messages = array();
        if (is_array($value)) {
            foreach ($value as $val) {
                if (!in_array($val, $this->argMap[$index][$name]['permittedValues'])) {
                    $messages[] = 'ERROR: argv ' . $name . '=' . $val . "\" not permitted";
                    $error = true;
                }
            }
        } elseif (!in_array($value, $this->argMap[$index][$name]['permittedValues'])) {
            $messages[] = 'ERROR: argv ' . $name . '=' . $value . "\" not permitted";
            $error = true;
        }
        if ($error) {
            $this->timeToDie($messages);
        }
    }

    /**
     * parse and store arguments
     * @param $args
     * @return void
     */
    private function _getArgs($args)
    {
        $arguments = $args;
        $this->exec = array_shift($arguments);
        $this->options = array();
        $this->flags = array();
        $this->arguments = array();

        while (sizeof($arguments) > 0) {
            $arg = array_shift($arguments);
            // Is it an flag? (prefixed with --)
            if (substr($arg, 0, 2) === '--') {
                $this->flags[substr($arg, 2)] = substr($arg, 2);
                continue;
            }

            // is it a flag? (prefixed with -)
            if (substr($arg, 0, 1) === '-') {
                $option = substr($arg, 1);

                if (strpos($option, '=') !== false) {
                    // it is the '=' syntax (-option=value)
                    $option = explode('=', $option);
                    $this->getValues($option[0], $option[1]);
                } else {
                    // it is the ' ' syntax (-option value)
                    while (isset($arguments[0]) && substr($arguments[0], 0, 1) !== '-') {
                        $val = array_shift($arguments);
                        $this->getValues($option, $val);
                    }
                }
                continue;
            }

            // finally, it is not option, nor flag
            $this->arguments[] = $arg;
            continue;
        }

        //set defaults if there are any set
        foreach ($this->argMap as $typeName => $typeDef) {
            foreach ($typeDef as $argName => $argDef) {
                if (!isset($this->{$typeName}[$argName]) && isset($argDef['default'])) {
                    $this->{$typeName}[$argName] = $argDef['default'];
                }
            }
        }

        Debug::variable($this->exec, 'exec', 4, Config::$debugCLI, Debug::LOG);
        Debug::variable($this->flags, 'flags', 4, Config::$debugCLI, Debug::LOG);
        Debug::variable($this->options, 'options', 4, Config::$debugCLI, Debug::LOG);
        Debug::variable($this->arguments, 'arguments', 4, Config::$debugCLI, Debug::LOG);
    }

    /**
     * either store value as single value or array of values
     * @param $index
     * @param $value
     * @return void
     */
    private function getValues($index, $value)
    {
        if (strpos($value, ',') !== false) {
            $this->options[$index] = explode(',', $value);
        } else {
            $this->options[$index] = $value;
        }
    }
}
