<?php

$settings = [];

// Do not echo the errors
ini_set('display_errors', '0');

// Paths.
$settings['root'] = dirname(__DIR__);
$settings['datagator'] = $settings['root'] . '/includes';
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/html';

/**
 * General settings.
 */

// Debug
// Set your debug level and additional handlers in ['loggers']['gaterdata']['handlers']
// @see https://github.com/Seldaek/monolog Monolog documentation.
// @see https://github.com/theorchard/monolog-cascade Monolog Cascade documentation.
$settings['log']['path'] = '/var/log/apache2/admin.gaterdata.error.log';
$settings['log']['settings'] = [
  'version' => 1,
  'formatters' => [
    'spaced' => [
      'format' => "%datetime% %channel%.%level_name%  %message%\n",
      'include_stacktraces' => true
    ],
    'dashed' => [
      'format' => "%datetime%-%channel%.%level_name% - %message%\n"
    ],
  ],
  'handlers' => [
    'debug' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'DEBUG',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'info' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'INFO',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'notice' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'NOTICE',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'warning' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'WARNING',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'error' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'ERROR',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'critical' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'CRITICAL',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'alert' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'ALERT',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'emergency' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'EMERGENCY',
      'formatter' => 'spaced',
      'stream' => $settings['log']['path'],
    ],
    'stderr' => [
      'class' => 'Monolog\Handler\ErrorLogHandler',
      'formatter' => 'spaced',
    ],
  ],
  'loggers' => [
    'gaterdata' => [
      'handlers' => ['debug'],
    ],
  ],
];

// Database.
// @see http://adodb.org/dokuwiki/doku.php Documentation of ADOdb.
$settings['db'] = [
  'base' => $settings['datagator'] . '/db/dbBase.yaml',
  'driver' => 'mysqli',
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'secret',
  'database' => 'gaterdata',
  'options' => [
    'debug' => FALSE,
  ],
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci',
];
define('ADODB_ERROR_LOG_TYPE', 3);
define('ADODB_ERROR_LOG_DEST', $settings['log']['path']);

// Email.
$settings['mail'] = [
  'from' => [
    'email' => 'example@gaterdata.com',
    'name' => 'GaterData',
  ],
  'smtp' => TRUE,
  'host' => 'smtp1.example.com;smtp2.example.com',
  'auth' => TRUE,
  'username' => 'example@gaterdata.com',
  'password' => 'secret',
  'smtpSecure' => 'tls',
  'port' => 587,
  'debug' => 0,
];

// User.
$settings['user']['token_life'] = '+1 hour';

/**
 * Admin settings.
 */

// Twig.
$settings['twig'] = [
  'path' => $settings['datagator'] . '/admin/templates',
  'cache_enabled' => TRUE,
  'cache_path' =>  $settings['temp'] . '/twig_cache',
];

// Slim.
$settings['displayErrorDetails'] = TRUE;
$settings['determineRouteBeforeAppMiddleware'] = TRUE;

return $settings;
