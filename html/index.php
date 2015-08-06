<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use Datagator\Config;
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
} catch (ApiException $e) {
  $output = $api->getOutputObj(
    $api->parseType(getallheaders(), 'Accept', 'json'),
    new Core\Error($e->getCode(), $e->getProcessor(), $e->getMessage()),
    $e->getHtmlCode()
  );
  echo $output->process();
  ob_end_flush();
  exit();
} catch (Exception $e) {
  echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
  ob_end_flush();
  exit();
}

ob_end_flush();

echo $result;
exit();
