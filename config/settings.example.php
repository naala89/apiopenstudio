<?php

$settings = [];

// Slim settings
$settings['displayErrorDetails'] = true;
$settings['determineRouteBeforeAppMiddleware'] = true;

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/html';

// View settings
$settings['twig'] = [
  'path' => $settings['root'] . '/includes/admin/templates',
  'cache_enabled' => false,
  'cache_path' =>  $settings['temp'] . '/twig_cache'
];

$settings['db'] = [
  'driver' => 'mysqli',
  'host' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
  'options' => [],
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci'
];

return $settings;
