<?php

if (file_exists(__DIR__ . '/public/' . $_SERVER['REQUEST_URI'])) {
    return false;
}

$_GET['request'] = $_SERVER["REQUEST_URI"];
include dirname(__DIR__) . '/public/index.php';
