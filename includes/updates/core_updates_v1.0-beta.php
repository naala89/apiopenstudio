<?php

/**
 * Update functions for ApiOpenStudio v1.0-beta
 *
 * @package    ApiOpenStudio
 * @subpackage Updates
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

use ApiOpenStudio\Cli\Install;

/**
 * Change to authentication token processes.
 *
 * Part 1 - Remove old token columns from the user DB
 *
 * @param ADODB_mysqli $db
 *
 * @version V1.0.0-beta1
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/101
 */
function change_to_auth_process_101(ADODB_mysqli $db)
{
    // Drop the unused token coumns from the user table.
    echo "Dropping old/unused user token columns\n";
    echo "Dropping user.token...\n";
    $sql = "ALTER TABLE user DROP COLUMN token";
    if (!$db->execute($sql)) {
        echo "Deleting column failed, please check the logs\n";
        exit;
    }
    echo "Dropping user.token_ttl...\n";
    $sql = "ALTER TABLE user DROP COLUMN token_ttl";
    if (!$db->execute($sql)) {
        echo "Deleting column failed, please check the logs\n";
        exit;
    }

    // Update all core resources.
    echo "Removing all core resources from the DB...\n";
    $sql = 'DELETE FROM resource WHERE appid = ';
    $sql .= '(SELECT appid from application where appid = (SELECT accid FROM account WHERE name="apiopenstudio") ';
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
}
