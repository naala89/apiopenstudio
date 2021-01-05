#!/usr/bin/php -q
<?php
/**
 * Class CLIScript.
 *
 * @package    ApiOpenStudio
 * @subpackage Core\Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core\Cli;

use ApiOpenStudio\Core\Config;

/**
 * Class CLIScript
 *
 * Run a PHP class as a bash command.
 * This prevents PHP or browser timeout.
 */
abstract class CLIScript
{
    /**
     * The current executed argument.
     *
     * @var array Current executed arg.
     */
    protected $exec;

    /**
     * The command options array.
     *
     * @var array Command options.
     */
    protected $options;

    /**
     * The command flags array.
     *
     * @var array Command flags.
     */
    protected $flags;

    /**
     * The command arguments array.
     *
     * @var array Command args.
     */
    protected $arguments;

    /**
     * The command argument map array.
     *
     * @var array Command argument map.
     */
    protected $argMap;

    /**
     * CLIScript constructor.
     */
    public function __construct()
    {
    }

    /**
     * Load arguments for the function.
     *
     * @param array $argv CLI args.
     *
     * @return void
     */
    public function load(array $argv)
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
     * Execute the function.
     *
     * @param array $argv CLI args.
     *
     * @return void
     */
    public function exec(array $argv = null)
    {
        if (!empty($argv)) {
            $this->load($argv);
        }
    }

    /**
     * Abstract function help.
     *
     * @return void
     */
    protected function help()
    {
        echo "There is no help defined.\n";
    }

    /**
     * Helper function to die.
     *
     * @param string $msg Log message.
     *
     * @return void
     */
    protected function timeToDie(string $msg)
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
     * Validate supplied options.
     *
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
     * Validate supplied flags.
     *
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
     * Validate all required options are present.
     *
     * @param string $opt Option name.
     * @param mixed $index Option index.
     *
     * @return void
     */
    private function _validateRequired(string $opt, $index)
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
     * Validate all supplied arguments are allowed.
     *
     * @param string $name Arg name.
     * @param mixed $index Arg index.
     *
     * @return void
     */
    private function _validateAllowed(string $name, $index)
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
     * Validate any supplied arguments with multiple values are allowed.
     *
     * @param string $name Arg name.
     * @param mixed $value Arg value.
     * @param mixed $index Arg index.
     *
     * @return void
     */
    private function _validateMultiple(string $name, $value, $index)
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
     * Validate against any permitted value restrictions.
     *
     * @param string $name Arg name.
     * @param mixed $value Arg value.
     * @param mixed $index Arg index.
     *
     * @return void
     */
    private function _validatePermitted(string $name, $value, $index)
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
     * Parse and store arguments.
     *
     * @param array $args Cli arguments.
     *
     * @return void
     */
    private function _getArgs(array $args)
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
     * Either store value as single value or array of values
     *
     * @param mixed $index Option index.
     * @param mixed $value Option value.
     *
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
