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
 * @param ADOConnection $db
 */
function update_all_core_processors_beta(ADOConnection $db)
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
 * @param ADOConnection $db
 *
 * @throws ApiException
 *
 * @version V1.0.0-beta
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/48
 */
function add_open_api_columns(ADOConnection $db)
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
 * @param ADOConnection $db
 *
 * @version V1.0.0-beta
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/48
 */
function update_var_store_table(ADOConnection $db)
{
    $config = new Config();

    echo "Renaming var_store.value column to val in the var_store table...\n";
    $sql = <<<SQL
ALTER TABLE `var_store` CHANGE COLUMN IF EXISTS `value` `val` blob DEFAULT NULL COMMENT 'value of the var' AFTER `key`;
SQL;
    if (!$db->execute($sql)) {
        echo "Renaming var_store.value column failed, please check the logs\n";
        exit;
    }

    echo "Adding the the var_store.accid column to the var_store table...\n";
    $sql = <<<SQL
ALTER TABLE `var_store` ADD COLUMN IF NOT EXISTS `accid` INT UNSIGNED DEFAULT NULL COMMENT "account id" AFTER `vid`
SQL;
    if (!$db->execute($sql)) {
        echo "Adding var_store.accid column failed, please check the logs\n";
        exit;
    }

    echo "Making the the var_store.appid column nullable...\n";
    $sql = <<<SQL
ALTER TABLE `var_store` CHANGE COLUMN `appid` `appid` int(10) unsigned DEFAULT NULL COMMENT 'application id'
SQL;
    if (!$db->execute($sql)) {
        echo "Updating var_store.appid column failed, please check the logs\n";
        exit;
    }
}

/**
 * Update core: rename to installed_version, add column (module).
 *
 * @param ADOConnection $db
 *
 * @version V1.0.0-beta
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/196
 */
function update_core_table(ADOConnection $db)
{
    $config = new Config();

    $sql = <<<SQL
SHOW TABLE STATUS WHERE `Name` = 'core'
SQL;
    $result = $db->execute($sql);

    if ($result->recordCount() === 0) {
        echo "Cannot rename `core` table, skipping...\n";
    } else {
        echo "Renaming the `core` table to `installed_version`...\n";
        $sql = <<<SQL
RENAME TABLE `core` TO `installed_version`
SQL;
        if (!$db->execute($sql)) {
            echo "Renaming the `core` table to `installed_version` failed, please check the logs\n";
        }
    }

    echo "Adding the `installed_version`.`mid` column...\n";
    $sql = <<<SQL
ALTER TABLE `installed_version`
    ADD COLUMN IF NOT EXISTS  `mid`
    int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'row ID'
SQL;
    if (!$db->execute($sql)) {
        echo "Adding the `installed_version`.`mid` column failed, please check the logs\n";
        exit;
    }

    echo "Adding the `installed_version`.`module` column...\n";
    $sql = <<<SQL
ALTER TABLE `installed_version`
    ADD COLUMN IF NOT EXISTS `module`
    varchar(256) NOT NULL DEFAULT '' COMMENT 'Module name' AFTER `mid`
SQL;
    if (!$db->execute($sql)) {
        echo "Adding the `installed_version`.`module` column failed, please check the logs\n";
        exit;
    }

    echo "Moving the `installed_version`.`version` column after `module`...\n";
    $sql = <<<SQL
ALTER TABLE `installed_version`
    CHANGE COLUMN `version`
    `version` varchar(255) NOT NULL DEFAULT '' COMMENT 'current version' AFTER `module`
SQL;
    if (!$db->execute($sql)) {
        echo "Modifying the `installed_version`.`version` column failed, please check the logs\n";
        exit;
    }

    echo "Adding module core to the existing core row in installed_version...\n";
    $sql = <<<SQL
UPDATE `installed_version` SET `module`="core" WHERE `module` IS NULL OR `module`=''
SQL;
    if (!$db->execute($sql)) {
        echo "Something went wrong while updating the installed_version.core version row, please check the logs\n";
        exit;
    }
}
