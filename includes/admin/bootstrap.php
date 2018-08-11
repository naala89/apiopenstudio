<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

// Instantiate the app
$app = new \Slim\App(['settings' => require dirname(__DIR__) . '/../config/settings.php']);

// Set up dependencies
require  __DIR__ . '/container.php';

// Register middleware
require __DIR__ . '/middleware.php';

// Register routes
require __DIR__ . '/routes.php';

return $app;
