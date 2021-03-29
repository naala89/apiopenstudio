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
 * @link      http://www.hashbangcode.com/
 */

/**
 * Populate test DB
 *
 * @file Populate test DB for gitlab pipelines functional tests.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

global $argv;

$install = new \ApiOpenStudio\Cli\Install();

$install->createLink(
    getenv('CI_MYSQL_DRIVER'),
    getenv('CI_MYSQL_HOST'),
    getenv('CI_MYSQL_DATABASE'),
    'root',
    getenv('CI_MYSQL_PASSWORD')
);
$install->createDatabase(getenv('CI_MYSQL_DATABASE'));
$install->createUser(getenv('CI_MYSQL_DATABASE'), getenv('CI_MYSQL_USER'), getenv('CI_MYSQL_PASSWORD'));
$install->useDatabase(getenv('CI_MYSQL_DATABASE'));
$install->createTables(getcwd(), '/includes/Db/dbDefinition.yaml', true);
$install->createResources(getcwd(), '/includes/resources');
$install->createAdminUser(getenv('CI_MYSQL_ADMIN_NAME'), getenv('CI_MYSQL_ADMIN_PASS'), getenv('CI_MYSQL_ADMIN_EMAIL'));

echo "Test database successfully setup.\n";
