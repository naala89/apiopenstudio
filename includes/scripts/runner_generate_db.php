<?php
/**
 * Populate test DB for gitlab pipelines functional tests.
 *
 * @package   Gaterdata
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @link      http://www.hashbangcode.com/
 */

/**
 * Populate test DB
 *
 * @file Populate test DB for gitlab pipelines functional tests.
 */

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// Create connection
$conn = new mysqli(
    getenv('MYSQL_HOST'),
    'root',
    getenv('MYSQL_ROOT_PASSWORD')
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$yaml = file_get_contents(dirname(dirname(__DIR__)) . '/includes/Db/dbDefinition.yaml');
$definition = \Spyc::YAMLLoadString($yaml);
