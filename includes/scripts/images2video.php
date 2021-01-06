#!/usr/bin/php
<?php

/**
 * Script to convert images to video.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 ApiOpenStudio
 * @link      https://www.apiopenstudio.com
 */

global $argv;
include_once(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../../config.php');
include_once(Config::$dirIncludes . 'cli/class.CLIImages2video.php');

$images2video = new CLI_images2video();
$images2video->exec($argv);

exit;
