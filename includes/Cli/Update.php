<?php

/**
 * Class Update.
 *
 * @package    ApiOpenStudio\Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Cli;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\SortByVersionTrait;
use Berlioz\PhpDoc\Exception\PhpDocException;
use Berlioz\PhpDoc\PhpDocFactory;
use Psr\SimpleCache\CacheException;
use ApiOpenStudio\Core\Utilities;

/**
 * Class Install
 *
 * Script to update the ApiOpenStudio database.
 */
class Update extends Script
{
    use SortByVersionTrait;
    use HandleExceptionTrait;

    /**
     * @var string Relative path to updates directory.
     */
    protected string $updateDir = 'includes/updates/';

    /**
     * {@inheritDoc}
     */
    protected array $argMap = [
        'options' => [
            'd' => [
                'required' => false,
                'multiple' => false,
            ],
        ],
        'flags' => [],
    ];

    /**
     * @var Config Config class.
     */
    protected Config $config;

    /**
     * @var ADOConnection database connection.
     */
    protected ADOConnection $db;

    /**
     * @var string Last update run.
     */
    protected string $lastUpdateVersionRun;

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
        $help = "Update\n\n";
        $help .= "This command will update ApiOpenStudio core.\n";
        $help .= "It will run all necessary core update functions in includes/updates.\n\n";
        $help .= "Options\n";
        $help .= "-d: (optional) full path to the update directory containing the update files.\n\n";
        $help .= "Examples:\n";
        $help .= "./vendor/bin/aos-update\n";
        $help .= "./vendor/bin/aos-update -d ./foobar\n";
        $help .= "./vendor/bin/aos-update -d ./foobar/\n";
        echo $help;
    }

    /**
     * Execute the function.
     *
     * @param array|null $argv
     *   CLI args.
     *
     * @return void
     */
    public function exec(array $argv = null)
    {
        parent::exec($argv);
        // Override the default update directory if option -d is input.
        if (!empty($this->options['d'])) {
            $this->updateDir = $this->options['d'];
        } else {
            try {
                $this->updateDir = $this->config->__get(['api', 'base_path']) . $this->updateDir;
            } catch (ApiException $e) {
                $this->handleException($e);
            }
        }
        if (substr($this->updateDir, -1) != '/') {
            $this->updateDir = $this->updateDir . '/';
        }

        $response = '';
        while ($response != 'y' && $response != 'n') {
            $prompt = 'Continuing will update database. It is recommended to make a backup beforehand. ';
            $prompt .= 'Continue [Y/n]: ';
            $response = $this->readlineTerminal($prompt);
            $response = empty($response) ? 'y' : strtolower($response);
        }
        if ($response != 'y') {
            echo "Exiting update...\n";
            exit;
        }

        try {
            $this->db = Utilities::getDbConnection($this->config->__get(['db']));
        } catch (ApiException $e) {
            $this->handleException($e);
        }

        $currentVersion = $this->getCurrentVersion();
        echo "\n";
        $functions = $this->findUpdates($currentVersion);
        echo "\n";
        if (empty($functions)) {
            echo "No updates to run!\n";
            exit;
        }
        $this->runUpdates($functions);
        echo "\n";
    }

    /**
     * Get the current version from the database.
     *
     * @return mixed
     *
     * @todo Refactor this function to use InstalledVersionMapper, after the next release.
     */
    protected function getCurrentVersion()
    {
        echo "Finding current version...\n";
        $sql = 'SHOW TABLE STATUS WHERE `Name` = "core"';
        if (!$this->db->GetRow($sql)) {
            $sql = 'SELECT `version` FROM `installed_version` WHERE `module`="core"';
        } else {
            $sql = 'SELECT version FROM core';
        }
        $row = $this->db->GetRow($sql);
        $version = $row['version'];
        if (empty($version)) {
            echo "Could not find current version, exiting...\n";
            exit;
        }
        echo "Current version: $version\n";
        $this->lastUpdateVersionRun = $version;
        return $version;
    }

    /**
     * Find all update functions.
     *
     * @param string $currentVersion
     *   Current version.
     *
     * @return array
     *   All functions with meta.
     */
    protected function findUpdates(string $currentVersion): array
    {
        echo "Scanning " . $this->updateDir . " for updates...\n";
        $currentVersion = trim(str_ireplace('v', '', $currentVersion));
        $phpDocFactory = new PhpDocFactory();
        $files = glob($this->updateDir . '*.php');
        $result = [];

        foreach ($files as $file) {
            include $file;
            $functions = Utilities::getDefinedFunctionsInFile($file);
            if (empty($functions)) {
                echo "Error: no updates found in $file\n";
                exit;
            }
            foreach ($functions as $function) {
                try {
                    $docblock = $phpDocFactory->getFunctionDoc($function);
                } catch (PhpDocException | CacheException $e) {
                    echo "An exception was thrown while searching for updates: " . $e->getMessage() . "\n";
                    exit;
                }
                if (!$docblock->hasTag('version')) {
                    echo "Skipping $function: No version found in the PHPDoc\n";
                    continue;
                }
                $version = $docblock->getTag('version')[0]->getValue();
                if (!preg_match("/([vV])+\\s?([0-9]\\.){2}[0-9]/", $version)) {
                    echo "Error: Invalid version found in the PHPDoc in $function\n";
                    exit;
                }
                $version = trim(str_ireplace('v', '', $version));
                if ($this->sortByVersion($version, $currentVersion) > 0) {
                    $result[$function] = $version;
                }
            }
        }

        if (!uasort($result, [$this, 'sortByVersion'])) {
            echo "Error: Failed to sort the update functions\n";
            exit;
        }

        return $result;
    }

    /**
     * Run the update,
     *
     * @param array $functions
     *   Ordered array of functions name to run for the update.
     */
    protected function runUpdates(array $functions)
    {
        foreach ($functions as $function => $version) {
            echo "Running update $function\n";
            $function($this->db);
            if ($this->sortByVersion($version, $this->lastUpdateVersionRun) > 0) {
                $this->lastUpdateVersionRun = $version;
                $sql = 'SHOW TABLE STATUS WHERE `Name` = "core"';
                if (!$this->db->GetRow($sql)) {
                    $sql = 'UPDATE `installed_version` SET version="' . $version . '" WHERE `module`="core"';
                } else {
                    $sql = 'UPDATE `core` SET version = "' . $version . '"';
                }
                if (!$this->db->execute($sql)) {
                    echo "Error: failed to update core version to $version";
                    exit;
                }
            }
            echo "Update $function complete\n";
        }
    }
}
