<?php

$settings = [];

// Slim settings.
$settings['displayErrorDetails'] = true;
$settings['determineRouteBeforeAppMiddleware'] = true;

// Path settings.
$settings['root'] = dirname(__DIR__);
$settings['datagator'] = $settings['root'] . '/includes';
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/html';

// View settings.
$settings['twig'] = [
  'path' => $settings['datagator'] . '/admin/templates',
  'cache_enabled' => false,
  'cache_path' =>  $settings['temp'] . '/twig_cache',
];

// User settings.
$settings['user']['token_life'] = '+1 hour';

// Database settings.
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

// Email settings.
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

return $settings;
