<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use Datagator\config;
use Datagator\Core;

Config::load();

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $api = new Core\Api(Config::$cache);
  $result = $api->process();
} catch (Core\ApiException $e) {
  $error = new Core\Error($e->getCode(), $e->getProcessor(), $e->getMessage());
  $class = 'Datagator\\Output\\' . ucfirst($api->parseType(getallheaders(), 'Accept', 'json'));
  $output = new $class($error->process(), $e->getHtmlCode());
  echo $output->process();
  ob_end_flush();
  exit();
} catch (Exception $e) {
  echo 'Error: ' . $e->getCode() . '. ' . $e->getMessage();
  ob_end_flush();
  exit();
}

ob_end_flush();

echo $result;
exit();
