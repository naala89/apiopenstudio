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
use ApiOpenStudio\Core\Request;

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

    $outputType = $api->getAccept($defaultFormat)['mimeType'];
    $outputType = $outputType == 'image' ? $defaultFormat : $outputType;
    if ($outputType == 'text' || $outputType == 'plain') {
        $logger->error('api', $e->getMessage());
        http_response_code($e->getHtmlCode());
        echo 'Error: ' . $e->getMessage();
        exit();
    }
    $outputClass = 'ApiOpenStudio\\Output\\' . ucfirst($outputType);
    if (!class_exists($outputClass)) {
        $message = 'Error: no default response format defined in the config and none defined in the request!';
        $logger->error('api', $message);
        echo $message;
        exit();
    }

    $error = new Error($e->getCode(), $e->getProcessor(), $e->getMessage());
    $dataContainer = $error->process();
    $output = new $outputClass(
        ['processor' => $outputType, 'id' => 'header defined output'], new Request(), $logger, $dataContainer, 400
    );
    ob_end_flush();
    echo $output->process()->getData();
    exit();
}

ob_end_flush();

echo $result->getData();
exit();
