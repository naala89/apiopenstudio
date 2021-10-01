<?php

/**
 * Class Script.
 *
 * @package    ApiOpenStudio
 * @subpackage Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Cli;

/**
 * Class Script
 *
 * Run a PHP class as a bash command. This prevents PHP or browser timeout.
 */
abstract class Script
{
    /**
     * The current executed argument.
     *
     * @var array Current executed arg.
     */
    protected array $exec;

    /**
     * The command options array.
     *
     * @var array Command options.
     */
    protected array $options;

    /**
     * The command flags array.
     *
     * @var array Command flags.
     */
    protected array $flags;

    /**
     * The command arguments array.
     *
     * @var array Command args.
     */
    protected array $arguments;

    /**
     * The command argument map array.
     *
     * @var array Command argument map.
     */
    protected array $argMap;

    /**
     * Script constructor.
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
        $this->getArgs($argv);
        $this->validateOptions();
        $this->validateFlags();
    }

    /**
     * Execute the function.
     *
     * @param array|null $argv
     *   CLI args.
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
     * Fetch a parameter from the command line.
     *
     * @param string $prompt
     *   Input prompt text.
     *
     * @return false|string
     *   Response in from the user.
     */
    protected function readlineTerminal(string $prompt = '')
    {
        $prompt && print $prompt;
        $terminal_device = '/dev/tty';
        $h = fopen($terminal_device, 'r');
        if ($h === false) {
            #throw new RuntimeException("Failed to open terminal device $terminal_device");
            return false; # probably not running in a terminal.
        }
        $line = rtrim(fgets($h), "\r\n");
        fclose($h);
        return $line;
    }

    /**
     * Helper function to die.
     *
     * @param string|array $msg Log message.
     *
     * @return void
     */
    protected function timeToDie($msg)
    {
        if (is_array($msg)) {
            foreach ($msg as $m) {
                echo "$m\n";
            }
        } else {
            echo "$msg\n";
        }
        die("Error: controlled termination of cli script.\n");
    }

    /**
     * Validate supplied options.
     *
     * @return void
     */
    private function validateOptions()
    {
        $this->validateRequired($this->options, 'options');
        foreach ($this->options as $name => $value) {
            $this->validateAllowed($name, 'options');
            $this->validateMultiple($name, $value, 'options');
            if (isset($this->argMap['options'][$name]['permittedValues'])) {
                $this->validatePermitted($name, $value, 'options');
            }
        }
    }

    /**
     * Validate supplied flags.
     *
     * @return void
     */
    private function validateFlags()
    {
        foreach ($this->flags as $name => $value) {
            $this->validateAllowed($name, 'flags');
        }
    }

    /**
     * Validate all required options are present.
     *
     * @param array $opt Option name.
     * @param mixed $index Option index.
     *
     * @return void
     */
    private function validateRequired(array $opt, $index)
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
    private function validateAllowed(string $name, $index)
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
    private function validateMultiple(string $name, $value, $index)
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
    private function validatePermitted(string $name, $value, $index)
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
    private function getArgs(array $args)
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
