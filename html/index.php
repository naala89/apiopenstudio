<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Datagator\Core\ApiException;
use Datagator\Core\Api;
use Datagator\Config;
use Datagator\Core\Error;
use Datagator\Output\Json;

\Datagator\Config::load();

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $api = new Api(Config::$cache);
  $result = $api->process();
}
catch (ApiException $e) {
  $outputClass = 'Datagator\\Output\\' . ucfirst($api->getAccept(Config::$defaultFormat));
  if (!class_exists($outputClass)) {
    $error = new Error(3, -1, 'invalid Accept header');
    $output = new Json($error->process(), $e->getHtmlCode());
    ob_end_flush();
    echo $output->process();
    exit();
  }
  $error = new Error($e->getCode(), $e->getProcessor(), $e->getMessage());
  $output = new $outputClass($error->process(), $e->getHtmlCode());
  ob_end_flush();
  echo $output->process();
  exit();
}
catch (Exception $e) {
  ob_end_flush();
  echo 'Error: ' . $e->getCode() . '. ' . $e->getMessage();
  exit();
}

ob_end_flush();

echo $result;
exit();
