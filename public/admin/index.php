<?php
/**
 * GaterData Admin page.
 *
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

/** @var Slim\App $app */
$app = require dirname(dirname(__DIR__)) . '/includes/Admin/bootstrap.php';

// Start
$app->run();
