<?php

/**
 * Class StreamLogger.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Class StreamLogger
 *
 * Extend Monolog so that a single instance can output to individual file streams.
 */
class StreamLogger
{
    /**
     * Associative array of name => logger.
     *
     * @var array $loggers
     */
    protected array $loggers;

    /**
     * Constructor.
     *
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->loggers = [];
        $formatter = new LineFormatter(null, null, false, true);
        foreach ($definitions as $loggerName => $definition) {
            $logger = new Logger($loggerName);
            $handler = new $definition['class']($definition['stream'], $definition['level']);
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
            $this->loggers[$loggerName] = $logger;
        }
    }

    /**
     * Add a logger.
     *
     * @param string $loggerName
     *   Name of the logger.
     * @param Logger $logger
     *   The logger.
     */
    public function add(string $loggerName, Logger $logger)
    {
        $this->loggers[$loggerName] = $logger;
    }

    /**
     * Remove a logger.
     *
     * @param string $loggerName
     *   Name of the logger.
     */
    public function remove(string $loggerName)
    {
        if (isset($this->loggers[$loggerName])) {
            unset($this->loggers[$loggerName]);
        }
    }

    /**
     * Fetch the names of all the loggers added.
     *
     * @return string[]
     *   Array of logger names.
     */
    public function loggerNames(): array
    {
        return array_keys($this->loggers);
    }

    /**
     * Send a debug message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function debug(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'debug', $message);
    }

    /**
     * Send an info message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function info(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'info', $message);
    }

    /**
     * Send a notice message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function notice(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'notice', $message);
    }

    /**
     * Send a warning message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function warning(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'warning', $message);
    }

    /**
     * Send an error message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function error(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'error', $message);
    }

    /**
     * Send a critical message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function critical(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'critical', $message);
    }

    /**
     * Send an alert message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function alert(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'alert', $message);
    }

    /**
     * Send an emergency message to a logger.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $message
     *   Message to log.
     *
     * @throws ApiException
     */
    public function emergency(string $loggerName, string $message)
    {
        $this->logMessage($loggerName, 'emergency', $message);
    }

    /**
     * log the message.
     *
     * @param string $loggerName
     *   Name of the logger to use.
     * @param string $level
     *   Log level.
     * @param string $logMessage
     *   Log message.
     *
     * @throws ApiException
     */
    protected function logMessage(string $loggerName, string $level, string $logMessage)
    {
        if (!isset($this->loggers[$loggerName])) {
            throw new ApiException("invalid logger called: $loggerName");
        }
        try {
            $this->loggers[$loggerName]->{$level}($logMessage);
        } catch (Exception $e) {
            throw new ApiException("invalid logger called: $loggerName. " . $e->getMessage());
        }
    }
}
