<?php
/**
 * ApiOpenStudio Admin page.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 ApiOpenStudio
 * @link      https://www.apiopenstudio.com
 */

/**
 * Slim application.
 *
 * @var Slim\App $app
 */
$app = require dirname(dirname(__DIR__)) . '/includes/Admin/bootstrap.php';

// Start
$app->run();
