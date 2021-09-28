#!/usr/bin/env php
<?php

/**
 * Script to update the ApiOpenStudio DB.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *            If a copy of the license was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

global $argv;

$install = new \ApiOpenStudio\Cli\Update();

$install->exec($argv);

echo "ApiOpenStudio is successfully updated!\n\n";

exit;
