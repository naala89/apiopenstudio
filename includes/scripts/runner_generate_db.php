<?php

/**
 * Populate test DB for gitlab pipelines functional tests.
 *
 * @package   ApiOpenStudio
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 ApiOpenStudio
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
    echo "Connection failed: " . $conn->connect_error . "\n";
    exit(1);
}
echo "Connected successfully\n";

// Create the database, user and permissions.
$sql = 'CREATE DATABASE IF NOT EXISTS `' . getenv('MYSQL_DATABASE') . '`';
if ($conn->query($sql)) {
    echo "Create database success\n";
} else {
    echo "Create database fail\n";
    exit(1);
}
$sql = 'CREATE USER IF NOT EXISTS "' . getenv('MYSQL_USERNAME') . '"@"';
$sql .= getenv('MYSQL_HOST') . '" IDENTIFIED BY "' . getenv('MYSQL_PASSWORD') . '"';
if ($conn->query($sql)) {
    echo "Create user success\n";
} else {
    echo "Create user fail\n";
    exit(1);
}
$sql = 'GRANT ALL PRIVILEGES ON *.' . getenv('MYSQL_DATABASE') . ' TO "' . getenv('MYSQL_USERNAME');
$sql .= '"@"' . getenv('MYSQL_HOST') . '"';
if ($conn->query($sql)) {
    echo "Grant privileges success\n";
} else {
    echo "Grant privileges fail\n";
    exit(1);
}
$sql = 'FLUSH PRIVILEGES';
if ($conn->query($sql)) {
    echo "Flush privileges success\n";
} else {
    echo "Flush privileges fail\n";
    exit(1);
}

// Drop connection and login again as user.
mysqli_close($conn);
$conn = new mysqli(
    getenv('MYSQL_HOST'),
    getenv('MYSQL_USERNAME'),
    getenv('MYSQL_PASSWORD')
);
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error . "\n";
    exit(1);
}
echo "Reconnected as ApiOpenStudio user successfully\n";

$sql = 'USE ' . getenv('MYSQL_DATABASE');
if ($conn->query($sql)) {
    echo "Use database success\n";
} else {
    echo "Use database fail\n";
    exit(1);
}

$yaml = file_get_contents(dirname(dirname(__DIR__)) . '/includes/Db/dbDefinition.yaml');
$definition = \Spyc::YAMLLoadString($yaml);

// Parse the DB  table definition array.
foreach ($definition as $table => $tableData) {
    $sqlPrimary = '';
    $sqlColumns = [];
    foreach ($tableData['columns'] as $column => $columnData) {
        // Column definitions.
        $sqlColumn = "`$column` ";
        if (!isset($columnData['type'])) {
            echo "CREATE TABLE `$table` fail!";
            echo 'Type missing in the metadata.';
            exit(1);
        }
        $sqlColumn .= ' ' . $columnData['type'];
        $sqlColumn .= isset($columnData['notnull']) && $columnData['notnull'] ? ' NOT null' : '';
        $sqlColumn .= isset($columnData['default']) ? (' DEFAULT ' . $columnData['default']) : '';
        $sqlColumn .= isset($columnData['autoincrement']) ? ' AUTO_INCREMENT' : '';
        $sqlColumn .= isset($columnData['primary']) ? ' PRIMARY KEY' : '';
        $sqlColumn .= isset($columnData['comment']) ? (" COMMENT '" . $columnData['comment'] . "'") : '';
        $sqlColumns[] = $sqlColumn;
    }
    $sql = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $sqlColumns) . ');';
    echo "$sql\n";
    if ($conn->query($sql)) {
        echo "Create table success\n";
    } else {
        echo "Create table fail\n";
        exit(1);
    }

    // Add data if required.
    if (isset($tableData['data'])) {
        foreach ($tableData['data'] as $row) {
            $keys = [];
            $values = [];
            foreach ($row as $key => $value) {
                $keys[] = "`$key`";
                $values[] = is_string($value) ? "\"$value\"" : $value;
            }
            $sql = "INSERT INTO `$table` (" . implode(', ', $keys) . ')';
            $sql .= 'VALUES (' . implode(', ', $values) . ');';
            echo "$sql\n";
            if ($conn->query($sql)) {
                echo "Table insert success\n";
            } else {
                echo "Table insert fail\n";
                exit(1);
            }
        }
    }
}

// Add resource data from the resources directory
$dir = dirname(dirname(__DIR__)) . '/includes/resources';
$filenames = scandir($dir);
foreach ($filenames as $filename) {
    if (pathinfo($filename, PATHINFO_EXTENSION) != 'yaml') {
        continue;
    }
    $yaml = \Spyc::YAMLLoadString(file_get_contents("$dir/$filename"));
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
    $sql = 'INSERT INTO resource (`appid`, `name`, `description`, `method`, `uri`, `meta`, `ttl`)';
    $sql .= "VALUES ($appid, '$name', '$description', '$method', '$uri', '$meta', $ttl)";
    echo "$sql\n";
    if ($conn->query($sql)) {
        echo "Insert resource success\n";
    } else {
        echo "Insert resource fail\n";
        exit(1);
    }
}
