<?php

/**
 * Class MonologWrapper.
 *
 * @package    ApiOpenStudio\Core
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
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\MongoDBHandler;
use Monolog\Handler\ProcessHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

/**
 * Class MonologWrapper
 *
 * Extend Monolog so that a single instance can output to individual file streams.
 */
class MonologWrapper
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
     *
     * @throws ApiException
     */
    public function __construct(array $definitions)
    {
        $this->loggers = [];
        $handlers = $formatters = [];

        // Define line formatters.
        foreach ($definitions['formatters'] as $formatterName => $definition) {
            $formatters[$formatterName] = new LineFormatter(
                $definition['format'],
                $definition['date_format'],
                $definition['allow_inline_line_breaks'],
                $definition['ignore_empty_context_and_extra']
            );
        }

        // Define handlers.
        foreach ($definitions['handlers'] as $handlerName => $definition) {
            $handlers[$handlerName] = $this->handler($handlerName, $definition);
            $handlers[$handlerName]->setFormatter($formatters[$definition['formatter']]);
        }

        // Create the Logger stream with assigned handlers.
        foreach ($definitions['loggers'] as $loggerName => $definition) {
            $logger = new Logger($loggerName);
            foreach ($definition['handlers'] as $handler) {
                $logger->pushHandler($handlers[$handler]);
            }
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
            throw new ApiException("Logger exception ($loggerName): " . $e->getMessage());
        }
    }

    /**
     * Generate a handler for the logging array.
     *
     * @param string $handlerName
     *   Name of the handler.
     * @param array $definition
     *   Handler attributes.
     *
     * @return ChromePHPHandler|ErrorLogHandler|FirePHPHandler|MongoDBHandler|ProcessHandler|StreamHandler|SyslogHandler
     *
     * @throws ApiException
     */
    protected function handler(string $handlerName, array $definition)
    {
        if (!isset($definition['class'])) {
            throw new ApiException("Missing class attribute in handler $handlerName in the settings");
        }
        switch ($definition['class']) {
            case 'StreamHandler':
                return $this->streamHandler($handlerName, $definition);
            case 'FirePHPHandler':
                return $this->firePHPHandler();
            case 'ChromePHPHandler':
                return $this->chromePHPHandler();
            case 'SyslogHandler':
                return $this->syslogHandler($handlerName, $definition);
            case 'ErrorLogHandler':
                return $this->errorLogHandler();
            case 'ProcessHandler':
                return $this->processHandler($handlerName, $definition);
            case 'MongoDBHandler':
                return $this->mongoDBHandler($handlerName, $definition);
            default:
                throw new ApiException("invalid handler class in $handlerName in the settings");
        }
    }

    /**
     * Create a StreamHandler object.
     *
     * @param string $handlerName
     *   Name of the handler.
     * @param array $definition
     *   Handler attributes.
     *
     * @return StreamHandler
     *
     * @throws ApiException
     */
    protected function streamHandler(string $handlerName, array $definition): StreamHandler
    {
        if (!isset($definition['stream']) || !isset($definition['level'])) {
            throw new ApiException("Missing stream or level in $handlerName handler in the settings");
        }
        return new StreamHandler($definition['stream'], $definition['level']);
    }

    /**
     * Create a FirePHPHandler object.
     *
     * @return FirePHPHandler
     */
    protected function firePHPHandler(): FirePHPHandler
    {
        return new FirePHPHandler();
    }

    /**
     * Create a ChromePHPHandler object.
     *
     * @return ChromePHPHandler
     */
    protected function chromePHPHandler(): ChromePHPHandler
    {
        return new ChromePHPHandler();
    }

    /**
     * Create a SyslogHandler object.
     *
     * @param string $handlerName
     *   Name of the handler.
     * @param array $definition
     *   Handler attributes.
     *
     * @return SyslogHandler
     *
     * @throws ApiException
     */
    protected function syslogHandler(string $handlerName, array $definition): SyslogHandler
    {
        if (!isset($definition['ident']) || !isset($definition['level']) || !isset($definition['facility'])) {
            throw new ApiException("Missing ident, facility or level in $handlerName handler in the settings");
        }
        return new SyslogHandler($definition['ident'], $definition['facility'], $definition['level']);
    }

    /**
     * Create an ErrorLogHandler object.
     *
     * @return ErrorLogHandler
     */
    protected function errorLogHandler(): ErrorLogHandler
    {
        return new ErrorLogHandler();
    }

    /**
     * Create a ProcessHandler object.
     *
     * @param string $handlerName
     *   Name of the handler.
     * @param array $definition
     *   Handler attributes.
     *
     * @return ProcessHandler
     *
     * @throws ApiException
     */
    protected function processHandler(string $handlerName, array $definition): ProcessHandler
    {
        if (!isset($definition['command']) || !isset($definition['level'])) {
            throw new ApiException("Missing command pr level attribute in $handlerName handler in the settings");
        }
        return new ProcessHandler($definition['command'], $definition['level']);
    }

    /**
     * Create a MongoDBHandler object.
     *
     * @param string $handlerName
     *   Name of the handler.
     * @param array $definition
     *   Handler attributes.
     *
     * @return MongoDBHandler
     *
     * @throws ApiException
     */
    protected function mongoDBHandler(string $handlerName, array $definition): MongoDBHandler
    {
        if (!isset($definition['mongodb']) || !isset($definition['collection']) || !isset($definition['level'])) {
            throw new ApiException(
                "Missing mongodb, level or collection attribute in $handlerName handler in the settings"
            );
        }
        return new MongoDBHandler($definition['mongodb'], $definition['collection'], $definition['level']);
    }
}
