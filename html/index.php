<?php

ob_start();

include_once(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../config.php');
include_once(Config::$dirIncludes . 'class.Api.php');

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $api = new Api(Config::$cache);
  $result = $api->process();
} catch (ApiException $e) {
  $output = $api->getOutputObj(
    $api->parseType(getallheaders(), 'Accept', 'json'),
    new Error($e->getCode(), $e->getProcessor(), $e->getMessage()),
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
