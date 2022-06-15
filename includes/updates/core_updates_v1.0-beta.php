<?php

/**
 * Update functions for ApiOpenStudio v1.0-beta
 *
 * @package    ApiOpenStudio\Updates
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

use ApiOpenStudio\Cli\Install;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;

/**
 * Update all core resources.
 *
 * @param ADODB_mysqli $db
 */
function update_all_core_processors_beta(ADODB_mysqli $db)
{
    // Update all core resources.
    echo "Removing all core resources from the DB...\n";
    $sql = 'DELETE FROM resource WHERE appid = ';
    $sql .= '(SELECT appid from application where accid = (SELECT accid FROM account WHERE name="apiopenstudio") ';
    $sql .= 'AND name = "core")';
    if (!$db->execute($sql)) {
        echo "Deleting core resources failed, please check the logs\n";
        exit;
    }
    echo "Regenerating all Core resources\n";
    $install = new Install();
    $install->createLink();
    $install->useDatabase();
    $install->createResources();
    $install->importOpenApi();
}

/**
 * Add the OpenApi columns
 *
 * @param ADODB_mysqli $db
 *
 * @throws ApiException
 *
 * @version V1.0.0-beta
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/48
 */
function add_open_api_columns(ADODB_mysqli $db)
{
    $config = new Config();

    echo "Adding the the openapi column to the application table...\n";
    try {
        $sql = "SELECT * FROM information_schema.COLUMNS ";
        $sql .= "WHERE TABLE_SCHEMA = '" . $config->__get(['db', 'database']) . "' ";
        $sql .= "AND TABLE_NAME = 'application' ";
        $sql .= "AND COLUMN_NAME = 'openapi'";
        $result = $db->execute($sql);
    } catch (ApiException $e) {
        echo 'An error occurred: ' . $e->getMessage() . "\n";
        exit;
    }
    if ($result->recordCount() !== 0) {
        echo "Cannot create application.openapi, it already exists\n";
    } else {
        // phpcs:ignore
        $sql = 'ALTER TABLE application ADD COLUMN openapi blob DEFAULT NULL COMMENT "JSON OpenApi definition for the group of resources"';
        if (!$db->execute($sql)) {
            echo "Adding column failed, please check the logs\n";
            exit;
        }
    }

    echo "Adding the the openapi column to the resource table...\n";
    $sql = "SELECT * FROM information_schema.COLUMNS ";
    $sql .= "WHERE TABLE_SCHEMA = '" . $config->__get(['db', 'database']) . "' ";
    $sql .= "AND TABLE_NAME = 'resource' ";
    $sql .= "AND COLUMN_NAME = 'openapi'";
    $result = $db->execute($sql);
    if ($result->recordCount() !== 0) {
        echo "Cannot create resource.openapi, it already exists\n";
    } else {
        // phpcs:ignore
        $sql = 'ALTER TABLE resource ADD COLUMN openapi blob DEFAULT NULL COMMENT "JSON OpenApi definition for the resource"';
        if (!$db->execute($sql)) {
            echo "Adding column failed, please check the logs\n";
            exit;
        }
    }

    update_all_core_processors_beta($db);
}

/**
 * Update var_store: Add accid column and make appid and value nullable.
 *
 * @param ADODB_mysqli $db
 *
 * @throws ApiException
 *
 * @version V1.0.0-beta
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/48
 */
function update_var_store_table(ADODB_mysqli $db)
{
    $config = new Config();

    echo "Adding the the accid column to the var_store table...\n";
    try {
        $sql = "SELECT * FROM information_schema.COLUMNS ";
        $sql .= "WHERE TABLE_SCHEMA = '" . $config->__get(['db', 'database']) . "' ";
        $sql .= "AND TABLE_NAME = 'var_store' ";
        $sql .= "AND COLUMN_NAME = 'accid'";
        $result = $db->execute($sql);
    } catch (ApiException $e) {
        echo 'An error occurred: ' . $e->getMessage() . "\n";
        exit;
    }
    if ($result->recordCount() !== 0) {
        echo "Cannot create var_store.accid, it already exists\n";
    } else {
        // phpcs:ignore
        $sql = <<<SQL
ALTER TABLE var_store 
ADD COLUMN accid INT UNSIGNED DEFAULT NULL COMMENT "account id"
AFTER `vid`
SQL;
        if (!$db->execute($sql)) {
            echo "Adding accid column failed, please check the logs\n";
            exit;
        }
    }

    $nullableTables = ['appid', 'value'];
    foreach ($nullableTables as $nullableTable) {
        echo "Validating the the $nullableTable column to be nullable...\n";
        $sql = <<<SQL
SELECT * 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = "?" 
AND TABLE_NAME = "var_store" 
AND COLUMN_NAME = "?"
SQL;
        $bindParams = [
            $config->__get(['db', 'database']),
            $nullableTable,
        ];
        $result = $db->execute($sql, $bindParams);
        if ($result->recordCount() !== 0) {
            echo "Cannot update var_store.$nullableTable, it does not exist\n";
            exit;
        }
    }
    $sql = "ALTER TABLE var_store MODIFY `appid` INT(10) UNSIGNED DEFAULT NULL COMMENT 'application id'";
    if (!$db->execute($sql)) {
        echo "updating appid column failed, please check the logs\n";
        exit;
    }
    $sql = 'ALTER TABLE var_store MODIFY `val` BLOB DEFAULT NULL COMMENT "value of the var"';
    if (!$db->execute($sql)) {
        echo "updating val column failed, please check the logs\n";
        exit;
    }
}
