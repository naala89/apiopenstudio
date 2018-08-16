<?php

/** @var Slim\App $app */
$app = require dirname(dirname(__DIR__)) . '/includes/admin/bootstrap.php';

// Start
$app->run();
