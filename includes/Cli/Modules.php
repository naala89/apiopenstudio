<?php

/**
 * Class Modules.
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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\ModuleHelper;

/**
 * Class Modules
 *
 * Script to Install a plugin or processor module in ApiOpenStudio
 */
class Modules extends Script
{
    /**
     * {@inheritDoc}
     */
    protected array $argMap = [
        'options' => [],
        'flags' => [
            'installed' => [],
            'uninstalled' => [],
            'list' => [],
            'install' => [],
            'uninstall' => [],
            'update' => [],
        ],
    ];

    /**
     * @var ModuleHelper
     */
    protected ModuleHelper $moduleHelper;

    /**
     * Install constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->moduleHelper = new ModuleHelper();
    }

    /**
     * {@inheritDoc}
     */
    protected function help()
    {
        $help = "Modules\n\n";
        $help .= "This command will install a plugin or processor module in ApiOpenStudio.\n\n";
        $help .= "Flags:\n";
        $help .= "  --list: list the modules in the codebase\n";
        $help .= "  --installed: list the modules that are installed\n";
        $help .= "  --uninstalled: list the modules that are not installed\n";
        $help .= "  --install: install one or more modules\n";
        $help .= "  --uninstall: uninstall one or more modules\n";
        $help .= "  --update: update one or more modules\n\n";
        $help .= "Examples:\n";
        $help .= "  ./includes/scripts/modules.php --list\n";
        $help .= "  ./includes/scripts/modules.php --installed\n";
        $help .= "  ./includes/scripts/modules.php --uninstalled\n";
        $help .= "  ./includes/scripts/modules.php --install \"\My\Processor\"\n";
        $help .= "  ./includes/scripts/modules.php --install \"\My\Processor\" \"\\Another\\Processor\"\n";
        $help .= "  ./includes/scripts/modules.php --uninstall \"\My\Processor\"\n";
        $help .= "  ./includes/scripts/modules.php --uninstall \"\My\Processor\" \"\\Another\\Processor\"\n";
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

        if (isset($this->flags['list'])) {
            $this->list();
        }
        if (isset($this->flags['installed'])) {
            $this->installed();
        }
        if (isset($this->flags['uninstalled'])) {
            $this->uninstalled();
        }
        if (isset($this->flags['install'])) {
            $this->install($this->arguments);
        }
        if (isset($this->flags['uninstall'])) {
            $this->uninstall($this->arguments);
        }
        if (isset($this->flags['update'])) {
            $this->update($this->arguments);
        }
        echo "Nothing to do\n";
        exit;
    }

    /**
     * Handle an ApiException.
     *
     * @param ApiException $e
     *
     * @return void
     */
    protected function handleException(ApiException $e)
    {
        echo "An error occurred, please check the logs.\n";
        echo $e->getMessage() . "\n";
        exit;
    }

    /**
     * Display a list of non-core plugins and processors installed by composer.
     *
     * @return void
     */
    protected function list()
    {
        echo "Non-core modules in the codebase (machine_name):\n";
        try {
            $modules = $this->moduleHelper->getModules();
            foreach (array_keys($modules) as $machineName) {
                echo "$machineName\n";
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Display a list of installed modules and versions.
     *
     * @return void
     */
    protected function installed()
    {
        echo "Installed modules:\n";
        try {
            $installedModules = $this->moduleHelper->getInstalled();
            foreach ($installedModules as $module => $version) {
                echo "$module: $version\n";
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Display a list of modules that are not installed.
     *
     * @return void
     */
    protected function uninstalled()
    {
        echo "Uninstalled modules:\n";
        try {
            $uninstalled = $this->moduleHelper->getUninstalled();
            foreach ($uninstalled as $module) {
                echo "$module\n";
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Install one or more modules.
     *
     * @param array $modules
     *
     * @return void
     */
    private function install(array $modules)
    {
        try {
            $installed = $this->moduleHelper->install($modules);
            echo "Modules installed:\n";
            foreach ($installed as $module) {
                echo "$module\n";
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Uninstall one or more modules.
     *
     * @param array $modules
     *
     * @return void
     */
    private function uninstall(array $modules)
    {
        try {
            $uninstalled = $this->moduleHelper->uninstall($modules);
            echo "Modules uninstalled:\n";
            foreach ($uninstalled as $module) {
                echo "$module\n";
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Update one or more modules.
     *
     * @param array $modules
     *
     * @return void
     */
    private function update(array $modules)
    {
        try {
            $updated = $this->moduleHelper->update($modules);
            echo "Modules updated:\n";
            foreach ($updated as $module => $functions) {
                echo "$module:\n";
                foreach ($functions as $function) {
                    echo "  $function\n";
                }
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }
}
