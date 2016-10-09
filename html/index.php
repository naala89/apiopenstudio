<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use Datagator\Core;

\Datagator\Config::load();

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $api = new Core\Api(\Datagator\Config::$cache);
  $result = $api->process();
} catch (Core\ApiException $e) {
  $error = new Core\Error($e->getCode(), $e->getProcessor(), $e->getMessage());
  $class = 'Datagator\\Output\\' . ucfirst($api->parseType('Accept', 'json'));
  $output = new $class($error->process(), $e->getHtmlCode());
  ob_end_flush();
  echo $output->process();
  exit();
} catch (Exception $e) {
  ob_end_flush();
  echo 'Error: ' . $e->getCode() . '. ' . $e->getMessage();
  exit();
}

ob_end_flush();

echo $result;
exit();
