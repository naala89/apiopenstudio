<?php

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Cascade\Cascade;
use Gaterdata\Core\Config;

session_start();

// Get the settings.
$config = new Config();
$settings = $config->all();

// Cascade::fileConfig($settings->__get(['log', 'settings']));

// Instantiate the app
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies.
require  __DIR__ . '/container.php';

// Register routes.
require __DIR__ . '/routes.php';

return $app;
