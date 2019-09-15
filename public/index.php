<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Gaterdata\Core\Config;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Api;
use Gaterdata\Core\Error;
use Gaterdata\Output\Json;

$config = new Config();

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $api = new Api($config->__get(['api', 'cache']));
  $result = $api->process();
}
catch (ApiException $e) {
  $outputClass = 'Gaterdata\\Output\\' . ucfirst($api->getAccept($config->__get(['api', 'defaultFormat'])));
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
