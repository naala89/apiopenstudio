<?php

/**
 * Class Install.
 *
 * @package    ApiOpenStudio
 * @subpackage Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Cli;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Db;

/**
 * Class Install
 *
 * Script to setup the ApiOpenStudio database.
 */
class Install extends Script
{
    /**
     * {@inheritDoc}
     */
    protected $argMap = [
        'options' => [],
        'flags' => [],
    ];

    /**
     * @var Config Config class.
     */
    protected $config;

    /**
     * @var ADONewConnection database connection.
     */
    protected $db;

    /**
     * Install constructor.
     */
    public function __construct()
    {
        $this->config = new Config();
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function help()
    {
        $help = "Install\n\n";
        $help .= "This command will create the database  and install ApiOpenStudio\n\n";
        $help .= "Example:\n";
        $help .= "./install.php\n";
        echo $help;
    }

    /**
     * {@inheritDoc}
     */
    public function exec(array $argv = null)
    {
        parent::exec($argv);

        $response = '';
        while ($response != 'y' && $response != 'n') {
            $prompt = 'Continuing will create a new database and erase the current database, ';
            $prompt .= 'if it exists, continue [Y/n]: ';
            $response = $this->readlineTerminal($prompt);
            $response = empty($response) ? 'y' : $response;
        }
        if ($response != 'y') {
            echo "Exiting install...\n";
            exit;
        }

        // DB link.
        $dsnOptionsArr = [];
        foreach ($this->config->__get(['db', 'options']) as $k => $v) {
            $dsnOptionsArr[] = "$k=$v";
        }
        $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
        $dsn = $this->config->__get(['db', 'driver']) . '://'
            . 'root' . ':'
            . $this->config->__get(['db', 'root_password']) . '@'
            . $this->config->__get(['db', 'host']) . '/'
            . $this->config->__get(['db', 'database']) . $dsnOptions;
        if (!$this->db = \ADONewConnection($dsn)) {
            echo "Error: DB connection failed, please check your settings.yml file.\n";
            exit;
        }

        $this->createDb();
        echo "\n";
        $this->createTables();
        echo "\n";
        $this->createResources();
        echo "\n";
        $this->createAdminUser();
        echo "\n";
    }

    /**
     * Create the Database.
     */
    protected function createDb()
    {
        // Create the database, user and permissions.
        echo "Creating the Database..\n";

        try {
            $sql = 'CREATE DATABASE ' . $this->config->__get(['db', 'database']) . 'IF NOT EXISTS';
            $this->db->execute($sql);
            $sql = 'CREATE USER IF NOT EXISTS "' . $this->config->__get(['db', 'username']) . '"@"';
            $sql .= $this->config->__get(['db', 'host'])
                . '" IDENTIFIED BY "'
                . $this->config->__get(['db', 'password'])
                . '"';
            $this->db->execute($sql);
            $sql = 'GRANT ALL PRIVILEGES ON * . * TO "' . $this->config->__get(['db', 'username']);
            $sql .= '"@"' . $this->config->__get(['db', 'host']) . '"';
            $this->db->execute($sql);
            $sql = 'FLUSH PRIVILEGES';
            $this->db->execute($sql);

            echo 'Database `' . $this->config->__get(['db', 'database']) . "` created successfully!\n";
        } catch (ApiException $e) {
            echo "Error creating the database:\n";
            echo $e->getMessage() . "\n";
            exit;
        }
    }

    /**
     * Create the tables anf populate them with initial Core data.
     *
     * @throws ApiException
     */
    protected function createTables()
    {
        echo "Creating and populating the tables with Core data...\n";

        $include_test = '';
        while ($include_test != 'y' && $include_test != 'n') {
            $prompt = 'Include test users and accounts [y/N]: ';
            $include_test = $this->readlineTerminal($prompt);
            $include_test = empty($include_test) ? 'n' : $include_test;
        }
        $path = $this->config->__get(['api', 'base_path']) . $this->config->__get(['db', 'definition_path']);
        $yaml = file_get_contents($path);
        $definition = \Spyc::YAMLLoadString($yaml);

        // Parse the DB  table definition array.
        foreach ($definition as $table => $tableData) {
            $sqlPrimary = '';
            $sqlColumns = [];
            foreach ($tableData['columns'] as $column => $columnData) {
                // Column definitions.
                $sqlColumn = "`$column` ";
                if (!isset($columnData['type'])) {
                    echo "CREATE TABLE `$table` failed!\n";
                    echo "Type missing in the metadata.\n";
                    exit;
                }
                $sqlColumn .= ' ' . $columnData['type'];
                $sqlColumn .= isset($columnData['notnull']) && $columnData['notnull'] ? ' NOT null' : '';
                $sqlColumn .= isset($columnData['default']) ? (' DEFAULT ' . $columnData['default']) : '';
                $sqlColumn .= isset($columnData['autoincrement']) ? ' AUTO_INCREMENT' : '';
                $sqlColumn .= isset($columnData['primary']) ? ' PRIMARY KEY' : '';
                $sqlColumn .= isset($columnData['comment']) ? (" COMMENT '" . $columnData['comment'] . "'") : '';
                $sqlColumns[] = $sqlColumn;
            }
            $sqlDrop = "DROP TABLE IF EXISTS `$table`";
            $this->db->execute($sqlDrop);
            $sqlCreate = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $sqlColumns) . ');';
            if (empty($this->db->execute($sqlCreate))) {
                // Stop if table create fails.
                echo "$sqlCreate\n";
                echo "CREATE TABLE `$table` fail!\n";
                exit;
            }

            // Add data if required.
            if (isset($tableData['data'])) {
                foreach ($tableData['data'] as $row) {
                    if ($table == 'application' && $row['name'] == 'testing' && !$include_test) {
                        // Do not create the testing application.
                        continue;
                    }
                    if (($table == 'user' || $table == 'user_role') && !$include_test) {
                        // Do not create the tester user and associated roles.
                        continue;
                    }
                    $keys = [];
                    $values = [];
                    foreach ($row as $key => $value) {
                        $keys[] = "`$key`";
                        $values[] = is_string($value) ? "\"$value\"" : $value;
                    }
                    $sqlRow = "INSERT INTO `$table` (" . implode(', ', $keys) . ')';
                    $sqlRow .= 'VALUES (' . implode(', ', $values) . ');';
                    if (empty($this->db->execute($sqlRow))) {
                        echo "$sqlRow\n";
                        echo "INSERT into `$table` fail!\n";
                        exit;
                    }
                }
            }
        }
        echo "Tables successfully initialised!\n";
    }

    /**
     * Add the Core resources to the DB.
     *
     * @throws ApiException
     */
    protected function createResources()
    {
        echo "Adding core resources to the database...\n";
        $dir = $this->config->__get(['api', 'base_path']) . $this->config->__get(['api', 'dir_resources']);
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
            $sqlRow = 'INSERT INTO resource (`appid`, `name`, `description`, `method`, `uri`, `meta`, `ttl`)';
            $sqlRow .= "VALUES ($appid, '$name', '$description', '$method', '$uri', '$meta', $ttl)";
            if (empty($this->db->execute($sqlRow))) {
                echo "$sqlRow\n";
                echo "INSERT $name into `resource` fail!\n";
                exit;
            }
        }
        echo "Resources successfully added to the DB!\n";
    }

    /**
     * Create administrator user.
     */
    protected function createAdminUser()
    {
        echo "Creating the admin user...\n";

        $username = $password = $email = '';
        while ($username == '') {
            $prompt = 'Enter the admin users username: ';
            $username = $this->readlineTerminal($prompt);
        }
        while ($password == '') {
            $prompt = 'Enter the admin users password: ';
            $password = $this->readlineTerminal($prompt);
        }
        while ($email == '') {
            $prompt = 'Enter the admin users email: ';
            $email = $this->readlineTerminal($prompt);
        }

        try {
            $userMapper = new Db\UserMapper($this->db);
            $user = new Db\User(
                null,
                1,
                $username,
                null,
                null,
                null,
                $email,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            );
            $user->setPassword($password);
            $userMapper->save($user);
        } catch (ApiException $e) {
            echo "Error: an error occurred creating your user\n";
            echo $e->getMessage() . "\n";
            exit;
        }
        echo "User $username created!\n";

        // Assign administrator role.
        echo "Assigning the administrator role to your use...\n";
        try {
            $user = $userMapper->findByUsername($username);
            $uid = $user->getUid();
            if (empty($uid)) {
                echo "Error: Could not find the newly created user.\n";
                exit;
            }
            $roleMapper = new Db\RoleMapper($this->db);
            $role = $roleMapper->findByName('Administrator');
            if (empty($uid)) {
                echo "Error: Could not find the administrator role.\n";
                exit;
            }
            $rid = $role->getRid();
            $userRole = new Db\UserRole(
                null,
                null,
                null,
                $uid,
                $rid
            );
            $userRoleMapper = new Db\UserRoleMapper($this->db);
            $userRoleMapper->save($userRole);
        } catch (ApiException $e) {
            echo "Error: An error occurred creating your Administrator role\n";
            echo $e->getMessage();
            exit;
        }

        echo "Administrator role successfully added to $username!\n\n";
    }
}
