<?php

/**
 * Update functions for ApiOpenStudio v1.0
 *
 * @package    ApiOpenStudio
 * @subpackage Updates
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

/**
 * Example update function.
 *
 * @param ADODB_mysqli $db
 *
 * @version V0.0.0
 */
function example_update(ADODB_mysqli $db)
{
    // Do Something
}

/**
 * Update all resource meta to use the 'processor' keyword instead of 'function'.
 *
 * @param ADODB_mysqli $db
 *
 * @version V1.0.0-alpha2
 *
 * @see https://gitlab.com/john89/api_open_studio/-/issues/54
 */
function update_all_resources_54(ADODB_mysqli $db)
{
    echo "Updating the meta for all resources...\n";
    $config = new \ApiOpenStudio\Core\Config();
    $coreAccount = $config->__get(['api', 'core_account']);
    $coreApplication = $config->__get(['api', 'core_application']);

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

    echo "Updating the Core 'Functions' resource\n";
    $sql = "SELECT res.* FROM resource AS res ";
    $sql .= "INNER JOIN application AS app ON res.appid = app.appid ";
    $sql .= "INNER JOIN account AS acc ON app.accid = acc.accid ";
    $sql .= "WHERE acc.name = '$coreAccount' ";
    $sql .= "AND app.name = '$coreApplication' ";
    $sql .= "AND res.name = 'Functions'";
    $resources = $db->execute($sql);
    if ($resources->recordCount() === 0) {
        echo "Unable to find the 'Functions' resource. Please validate the SQL: $sql\n";
        exit();
    }
    while ($resource = $resources->fetchRow()) {
        $resid = $resource['resid'];
        $name = $resource['name'];
        echo "Editing name, description and URL for $resid: $name\n";
        $sql = "UPDATE resource SET name = 'Processors', description = 'Ftech details of processors'";
        $sql .= ", uri = 'processors' WHERE resid = $resid";
        $db->execute($sql);
    }
}
