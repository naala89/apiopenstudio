<?php

/**
 * Populate test DB for gitlab pipelines functional tests.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *            If a copy of the license was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

/**
 * Populate test DB
 *
 * @file Populate test DB for gitlab pipelines functional tests.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

//global $argv;

$install = new \ApiOpenStudio\Cli\Install();
$config = new \ApiOpenStudio\Core\Config();

$driver = $config->__get(['db', 'driver']);
$host = $config->__get(['db', 'host']);
$database = $config->__get(['db', 'database']);
$username = $config->__get(['db', 'username']);
$password = $config->__get(['db', 'password']);
$basePath = dirname(__DIR__) . '/';
$resources = $config->__get(['api', 'dir_resources']);
$dbDefinition = $config->__get(['db', 'definition_path']);

$install->createLink(
    null,
    null,
    '',
    'root',
    getenv('MYSQL_ROOT_PASSWORD')
);
$install->createDatabase($database);
$install->createUser($database, $username, $password);
$install->useDatabase($database);
$install->createTables($basePath, $dbDefinition, true);
$install->createResources($basePath, $resources);
$install->createAdminUser(getenv('ADMIN_NAME'), getenv('ADMIN_PASS'), getenv('ADMIN_EMAIL'));

echo "Test database successfully setup.\n";
