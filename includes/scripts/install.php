#!/usr/bin/env php
<?php

/**
 * Script to install the ApiOpenStudio DB.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *            If a copy of the license was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use ApiOpenStudio\Cli\Install;

global $argv;

$install = new Install();

$install->exec($argv);

echo "ApiOpenStudio is successfully installed!\n\n";
echo "you will now be able to configure and use the admin GUI and/or make REST calls to Api OpenStudio.\n";

exit;
