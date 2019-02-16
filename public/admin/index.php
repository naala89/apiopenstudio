<?php

/** @var Slim\App $app */
$app = require dirname(dirname(__DIR__)) . '/includes/Admin/bootstrap.php';

// Start
$app->run();
