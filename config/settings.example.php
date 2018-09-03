<?php

$settings = [];

/**
 * General settings.
 */

// Paths.
$settings['root'] = dirname(__DIR__);
$settings['datagator'] = $settings['root'] . '/includes';
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/html';

ini_set("display_errors", "0"); # but do not echo the errors
define('ADODB_ERROR_LOG_TYPE', 3);
define('ADODB_ERROR_LOG_DEST', $settings['log']['path']);

// Debug
//$settings['log'] = [
//  'path' => '/var/www/sites/admin.gaterdata.error.log',
//  'level' => Monolog\Logger::DEBUG,
//];
$settings['log']['path'] = '/var/www/sites/admin.gaterdata.error.log';
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
    'chrome_console' => [
      'class' => 'Monolog\Handler\ChromePHPHandler',
      'level' => 'DEBUG',
      'formatter' => 'spaced',
    ],
    'info_file_handler' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'INFO',
      'formatter' => 'spaced',
      'stream' => './demo_info.log'
    ],
    'error_file_handler' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'ERROR',
      'stream' => './demo_error.log',
      'formatter' => 'spaced'
    ],
  ],
  'processors' => [
    'tag_processor' => [
      'class' => 'Monolog\Processor\TagProcessor'
    ],
  ],
  'loggers' => [
    'gaterdata' => [
      'handlers' => ['chrome_console', 'info_file_handler'],
    ],
  ],
];

// Database.
$settings['db'] = [
  'base' => $settings['datagator'] . '/db/dbBase.yaml',
  'driver' => 'mysqli',
  'host' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
  'options' => [
    'debug' => FALSE,
  ],
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci',
];

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
