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

// Debug
//$settings['log'] = [
//  'path' => '/var/log/apache2/admin.gaterdata.error.log',
//  'level' => \Monolog\Logger::DEBUG,
//];
$settings['log'] = [
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
    'console' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'DEBUG',
      'formatter' => 'spaced',
      'stream' => 'php://stdout'
    ],
    'info_file_handler' => [
      'class' => 'Monolog\Handler\StreamHandler',
      'level' => 'INFO',
      'formatter' => 'dashed',
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
    'my_logger' => [
      'handlers' => ['console', 'info_file_handler'],
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
  'options' => [],
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
