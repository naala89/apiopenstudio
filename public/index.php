<?php
/**
 * Main GaterData API entrypoint.
 *
 * @package   Gaterdata
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @link      https://gaterdata.com
 */

?>
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Gaterdata\Core\Config;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Api;
use Gaterdata\Core\Error;
use Cascade\Cascade;

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $config = new Config();
    $api = new Api($config->all());
    $result = $api->process();
} catch (ApiException $e) {
    $outputClass = 'Gaterdata\\Output\\' . ucfirst($api->getAccept($config->__get(['api', 'default_format'])));
    if (!class_exists($outputClass)) {
        echo 'Error: no default format defined in the config!';
        exit();
    }
    Cascade::fileConfig($config->__get(['debug']));
    $logger = Cascade::getLogger('api');
    $error = new Error($e->getCode(), $e->getProcessor(), $e->getMessage());
    $output = new $outputClass($error->process(), $e->getHtmlCode(), $logger);
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
