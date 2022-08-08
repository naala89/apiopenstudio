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
    use HandleExceptionTrait;

    /**
     * {@inheritDoc}
     */
    protected array $argMap = [
        'options' => [],
        'flags' => [
            'installed' => [],
            'uninstalled' => [],
            'updates' => [],
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
        $help .= "This command will allow you in install, uninstall, update and list plugins or processor modules in ApiOpenStudio.\n\n";
        $help .= "Flags:\n";
        $help .= "  --list: list the modules in the codebase\n";
        $help .= "  --installed: list the modules that are installed\n";
        $help .= "  --uninstalled: list the modules that are not installed\n";
        $help .= "  --updates: list the pending updates for all installed modules\n";
        $help .= "  --install: install one or more modules\n";
        $help .= "  --uninstall: uninstall one or more modules\n";
        $help .= "  --update: update one or more modules\n\n";
        $help .= "Examples:\n";
        $help .= "  ./vendor/bin/aos-modules --list\n";
        $help .= "  ./vendor/bin/aos-modules --installed\n";
        $help .= "  ./vendor/bin/aos-modules --uninstalled\n";
        $help .= "  ./vendor/bin/aos-modules --updates\n";
        $help .= "  ./vendor/bin/aos-modules --install \"\My\Processor\"\n";
        $help .= "  ./vendor/bin/aos-modules --install \"\My\Processor\" \"\\Another\\Processor\"\n";
        $help .= "  ./vendor/bin/aos-modules --uninstall \"\My\Processor\"\n";
        $help .= "  ./vendor/bin/aos-modules --uninstall \"\My\Processor\" \"\\Another\\Processor\"\n";
        $help .= "  ./vendor/bin/aos-modules --update \"\My\Processor\"\n";
        $help .= "  ./vendor/bin/aos-modules --update \"\My\Processor\" \"\\Another\\Processor\"\n";
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
        if (isset($this->flags['updates'])) {
            $this->updates();
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
     * Display a list of non-core plugins and processors installed by composer.
     *
     * @return void
     */
    protected function list()
    {
        echo "Contrib modules in the codebase:\n\n";
        try {
            $modules = $this->moduleHelper->getModules();
            if (empty($modules)) {
                echo "None\n";
            } else {
                foreach ($modules as $machineName => $info) {
                    echo "$machineName\n";
                    if ($info['installed']) {
                        echo '  Installed: ' . $info['installed'] . "\n";
                        if (empty($info['update_functions'])) {
                            echo "  Updates: None\n";
                        } else {
                            echo "  Updates:\n";
                            foreach ($info['update_functions'] as $updateFunction) {
                                echo "    $updateFunction\n";
                            }
                        }
                    } else {
                        echo "  Installed: false\n";
                    }
                }
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
            $modules = $this->moduleHelper->getInstalled();
            if (empty($modules)) {
                echo "None\n";
            } else {
                foreach ($modules as $machineName => $info) {
                    echo "$machineName\n";
                    if ($info['installed']) {
                        echo '  Installed: ' . $info['installed'] . "\n";
                        if (!empty($info['update_functions'])) {
                            echo "  Updates:\n";
                            foreach ($info['update_functions'] as $updateFunction) {
                                echo "    $updateFunction\n";
                            }
                        }
                    } else {
                        echo "  Installed: false\n";
                    }
                }
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
            $modules = $this->moduleHelper->getUninstalled();
            if (empty($modules)) {
                echo "None\n";
            } else {
                foreach ($modules as $machineName => $info) {
                    echo "$machineName\n";
                }
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Display a list of pending updates for all or a single module.
     *
     * @return void
     */
    protected function updates()
    {
        try {
            $updates = $this->moduleHelper->updates($this->arguments);
            if (empty($updates)) {
                echo "None\n";
            } else {
                echo "Module:\n";
                foreach ($updates as $machineName => $details) {
                    echo "  $machineName\n";
                    echo "    Updates\n";
                    foreach ($details['update_functions'] as $updateFunction => $version) {
                        echo "      $updateFunction ($version)\n";
                    }
                }
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
            if (empty($updated)) {
                echo "None\n";
            } else {
                foreach ($updated as $module => $functions) {
                    echo "$module:\n";
                    foreach ($functions as $function => $version) {
                        echo "  $function ($version)\n";
                    }
                }
            }
        } catch (ApiException $e) {
            $this->handleException($e);
        }
        exit;
    }
}
