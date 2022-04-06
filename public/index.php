<?php

/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package ApiOpenStudio
 */

/**
 * Main ApiOpenStudio API entrypoint.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *            If a copy of the license was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Api;
use ApiOpenStudio\Core\Error;
use ApiOpenStudio\Core\MonologWrapper;

ob_start();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

$config = new Config();

try {
    $api = new Api($config->all());
    $result = $api->process();
} catch (ApiException $e) {
    $api = new Api($config->all());
    $logger = new MonologWrapper($config->__get(['debug']));
    $defaultFormat = $config->__get(['api', 'default_format']);
    $outputClass = ucfirst($api->getAccept($defaultFormat));
    if ($outputClass == 'Text' || $outputClass == 'Plain') {
        $logger->error('api', $e->getMessage());
        echo 'Error: ' . $e->getMessage();
        exit();
    }
    $outputClass = 'ApiOpenStudio\\Output\\' . $outputClass;
    if (!class_exists($outputClass)) {
        $logger->error('api', 'Error: no default format defined in the config!');
        echo 'Error: no default format defined in the config!';
        exit();
    }
    $error = new Error($e->getCode(), $e->getProcessor(), $e->getMessage());
    $dataContainer = $error->process();
    $output = new $outputClass(
        $dataContainer,
        $e->getHtmlCode(),
        $logger
    );
    ob_end_flush();
    echo $output->process()->getData();
    exit();
} catch (Exception $e) {
    ob_end_flush();
    echo 'Error: ' . $e->getCode() . '. ' . $e->getMessage();
    exit();
}

ob_end_flush();

echo $result->getData();
exit();
