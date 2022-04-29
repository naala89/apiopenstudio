<?php

/**
 * Update functions for ApiOpenStudio v1.0-alpha
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
 * Update all resource meta to use the 'processor' keyword instead of 'function'.
 *
 * Part 1 - Update meta function key.
 *
 * @param ADODB_mysqli $db
 *
 * @version V1.0.0-alpha2
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/54
 */
function update_all_resources_54_part_1(ADODB_mysqli $db)
{
    echo "Updating the meta for all resources...\n";

    $sql = "SELECT * FROM resource";
    $resources = $db->execute($sql);
    while ($resource = $resources->fetchRow()) {
        $resid = $resource['resid'];
        $meta_old = $resource['meta'];
        $name = $resource['name'];
        echo "Checking resource $resid: $name.\n";
        $meta_new = str_ireplace('"function":', '"processor":', $meta_old);
        if ($meta_new == $meta_old) {
            echo "Nothing to update.\n";
        } else {
            echo "Updating resource $resid: $name\n";
            $sql = "UPDATE resource SET meta = '$meta_new' WHERE resid = $resid";
            $db->execute($sql);
        }
    }
}

/**
 * Update all resource meta to use the 'processor' keyword instead of 'function'.
 *
 * Part 2 - Update the functions processor.
 *
 * @param ADODB_mysqli $db
 *
 * @throws ApiException
 *
 * @version V1.0.0-alpha2
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/54
 */
function update_all_resources_54_part_2(ADODB_mysqli $db)
{
    echo "Updating the Core 'Functions' resource\n";

    $config = new Config();
    $coreAccount = $config->__get(['api', 'core_account']);
    $coreApplication = $config->__get(['api', 'core_application']);
    $basePath = $config->__get(['api', 'base_path']);
    $dirResources = $config->__get(['api', 'dir_resources']);

    // Find the old Functions processor in the DB.
    $sql = "SELECT res.* FROM resource AS res ";
    $sql .= "INNER JOIN application AS app ON res.appid = app.appid ";
    $sql .= "INNER JOIN account AS acc ON app.accid = acc.accid ";
    $sql .= "WHERE acc.name = '$coreAccount' ";
    $sql .= "AND app.name = '$coreApplication' ";
    $sql .= "AND res.name = 'Functions'";
    $resources = $db->execute($sql);
    if ($resources->recordCount() === 0) {
        $message = "Error: unable to find the 'Functions' resource for $coreAccount, $coreApplication.";
        $message .= " Please validate the SQL: $sql\n";
        echo $message;
        exit();
    }

    // Load the data from the new Processors processor file.
    $file = $basePath . $dirResources . 'processors.yaml';
    if (!$contents = file_get_contents($file)) {
        echo "Error: unable to find the new $file file!\n";
        exit();
    }
    $yaml = Spyc::YAMLLoadString($contents);
    $name = $yaml['name'];
    $description = $yaml['description'];
    $uri = $yaml['uri'];
    $method = $yaml['method'];
    $appid = $yaml['appid'];
    $ttl = $yaml['ttl'];
    $meta = [];
    if (!empty($yaml['security'])) {
        $meta[] = '"security": ' . json_encode($yaml['security']);
    }
    if (!empty($yaml['process'])) {
        $meta[] = '"process": ' . json_encode($yaml['process']);
    }
    $meta = '{' . implode(', ', $meta) . '}';

    // Delete the old Functions processor.
    while ($resource = $resources->fetchRow()) {
        $resid = $resource['resid'];
        echo "Deleting $resid: " . $resource['name'] . "'\n";
        $sql = "DELETE FROM resource WHERE resid = $resid";
        $db->execute($sql);
    }

    // Insert the new Processors processor.
    echo "Inserting new Processors processor\n";
    $sql = 'INSERT INTO resource (`appid`, `name`, `description`, `method`, `uri`, `meta`, `ttl`)';
    $sql .= "VALUES ($appid, '$name', '$description', '$method', '$uri', '$meta', $ttl)";
    if (!($db->execute($sql))) {
        echo "$sql\n";
        echo "Error: insert resource `$name` failed, please check your logs.\n";
        exit;
    }
}

/**
 * Change to authentication token processes.
 *
 * @param ADODB_mysqli $db
 *
 * @throws ApiException
 *
 * @version v1.0.0-alpha3
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/101
 */
function change_to_auth_process_101(ADODB_mysqli $db)
{
    $config = new Config();
    // Drop the unused token coumns from the user table.
    echo "Dropping old/unused user token columns\n";
    echo "Dropping user.token...\n";
    $sql = "SELECT * FROM information_schema.COLUMNS ";
    $sql .= "WHERE TABLE_SCHEMA = '" . $config->__get(['db', 'database']) . "' ";
    $sql .= "AND TABLE_NAME = 'user' ";
    $sql .= "AND COLUMN_NAME = 'token'";
    $result = $db->execute($sql);
    if ($result->recordCount() === 0) {
        echo "Cannot drop user.token, it does not exist\n";
    } else {
        $sql = "ALTER TABLE user DROP COLUMN token";
        if (!$db->execute($sql)) {
            echo "Deleting column failed, please check the logs\n";
            exit;
        }
    }
    echo "Dropping user.token_ttl...\n";
    $sql = "SELECT * FROM information_schema.COLUMNS ";
    $sql .= "WHERE TABLE_SCHEMA = '" . $config->__get(['db', 'database']) . "' ";
    $sql .= "AND TABLE_NAME = 'user' ";
    $sql .= "AND COLUMN_NAME = 'token_ttl'";
    $result = $db->execute($sql);
    if ($result->recordCount() === 0) {
        echo "Cannot drop user.token_ttl, it does not exist\n";
    } else {
        $sql = "ALTER TABLE user DROP COLUMN token_ttl";
        if (!$db->execute($sql)) {
            echo "Deleting column failed, please check the logs\n";
            exit;
        }
    }
}

/**
 * Update all core resources.
 *
 * @param ADODB_mysqli $db
 *
 * @version v1.0.0-alpha3
 *
 * @see https://gitlab.com/apiopenstudio/apiopenstudio/-/issues/127
 */
function change_to_auth_process_127(ADODB_mysqli $db)
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
}
