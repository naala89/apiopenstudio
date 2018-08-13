<?php

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require dirname(dirname(__DIR__)) . '/config/settings.php';
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies
require  __DIR__ . '/container.php';

// Register routes
require __DIR__ . '/routes.php';

return $app;
