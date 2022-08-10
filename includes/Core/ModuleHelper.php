<?php

namespace ApiOpenStudio\Core;

use ADOConnection;
use ApiOpenStudio\Db\InstalledVersion;
use ApiOpenStudio\Db\InstalledVersionMapper;
use Berlioz\PhpDoc\Exception\PhpDocException;
use Berlioz\PhpDoc\PhpDocFactory;
use Psr\SimpleCache\CacheException;
use ReflectionClass;
use ReflectionException;

class ModuleHelper
{
    use SortByVersionTrait;

    /**
     * @var array|string[] List of directories containing plugins and modules.
     */
    protected array $directories = [
        'Endpoint',
        'Output',
        'Processor',
        'Security',
    ];

    /**
     * @var ADOConnection
     */
    protected ADOConnection $db;

    /**
     * @var Config
     */
    protected Config $settings;

    /**
     * @var ListClassesInDirectory
     */
    protected ListClassesInDirectory $listClassesInDirectory;

    /**
     * @var ProcessorHelper
     */
    protected ProcessorHelper $processorHelper;

    /**
     * @var InstalledVersionMapper
     */
    protected InstalledVersionMapper $installedVersionMapper;

    /**
     * @var MonologWrapper
     */
    protected MonologWrapper $logger;

    /**
     * @var ComposerHelper
     */
    protected ComposerHelper $composerHelper;

    /**
     * @param ADOConnection|null $db
     *
     * @throws ApiException
     */
    public function __construct(ADOConnection $db = null)
    {
        $this->settings = new Config();
        if (!empty($db)) {
            $this->db = $db;
        } else {
            $dbSettings = $this->settings->__get(['db']);
            $this->db = Utilities::getDbConnection($dbSettings);
        }
        $this->logger = new MonologWrapper($this->settings->__get(['debug']));
        $this->processorHelper = new ProcessorHelper();
        $this->installedVersionMapper = new InstalledVersionMapper($this->db, $this->logger);
        $this->listClassesInDirectory = new ListClassesInDirectory();
        $this->composerHelper = new ComposerHelper();
    }

    /**
     * Return a list of all non-core modules and plugins in the codebase.
     *
     * @param bool $includeCore
     *
     * @return array
     *
     * @throws ApiException
     */
    public function getModules(bool $includeCore = false): array
    {
        $modules = [];

        // Get list of modules, with the details array.
        $includesDir = $this->settings->__get(['api', 'base_path']) . 'includes/';
        foreach ($this->directories as $directory) {
            $classNames = $this->listClassesInDirectory->listClassesInDirectory($includesDir . $directory);
            foreach ($classNames as $className) {
                $details = $this->getDetails($className);
                if (
                    $details !== false
                    && isset($details['details']['machineName'])
                    && ($includeCore || strrpos($details['details']['machineName'], "\\") !== false)
                ) {
                    $modules[$details['details']['machineName']] = $details;
                }
            }
        }

        // Get list of update functions for installed modules.
        foreach ($modules as $machineName => &$module) {
            $updateFilePath = dirname($module['path']) . '/update.php';
            $module['update_functions'] = $module['installed'] === false
                ? []
                : $this->getUpdateFunctions($updateFilePath, $machineName);
        }

        return $modules;
    }

    /**
     * Return an array of installed modules and their current version.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function getInstalled(): array
    {
        $allModules = $this->getModules();

        $installedModules = [];
        foreach ($allModules as $machineName => $module) {
            if ($module['installed'] !== false) {
                $installedModules[$machineName] = $module;
            }
        }

        return $installedModules;
    }

    /**
     * Return an array of uninstalled non-core modules and plugins.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function getUninstalled(): array
    {
        $allModules = $this->getModules();

        $installedModules = [];
        foreach ($allModules as $machineName => $module) {
            if ($module['installed'] === false) {
                $installedModules[$machineName] = $module;
            }
        }

        return $installedModules;
    }

    /**
     * Return modules details for all modules that require updates.
     *
     * @param array $modules
     *
     * @return array
     *
     * @throws ApiException
     */
    public function updates(array $modules = []): array
    {
        if (empty($modules)) {
            $allModules = $this->getModules();
        } else {
            $temp = $this->getModules();
            $allModules = [];
            foreach ($temp as $machineName => $details) {
                if (in_array($machineName, $modules)) {
                    $allModules[$machineName] = $details;
                }
            }
        }
        $updates = [];
        foreach ($allModules as $machineName => $details) {
            if (!empty($details['update_functions'])) {
                $updates[$machineName] = $details;
            }
        }
        return $updates;
    }

    /**
     * Install one or more modules.
     *
     * @param array $modules Array of machine_names.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function install(array $modules): array
    {
        if (empty($modules)) {
            throw new ApiException('No module machine_name given.', 0, 'oops');
        }

        $allModules = $this->getModules();
        $composerLock = $this->settings->__get(['api', 'base_path']) . 'composer.lock';
        $this->composerHelper->parseComposerLock($composerLock);

        foreach ($modules as $machineName) {
            if (!isset($allModules[$machineName])) {
                throw new ApiException("$machineName does not exist in the codebase.", 0, 'oops');
            }

            if ($this->getInstalledVersion($machineName) !== false) {
                throw new ApiException("$machineName already installed.", 0, 'oops');
            }

            $installFilePath = dirname($allModules[$machineName]['path']) . '/install.php';
            if (!file_exists($installFilePath)) {
                throw new ApiException("Cannot install, $installFilePath not found.", 0, 'oops');
            }

            include_once $installFilePath;
            $installFunction = substr($machineName, 0, strrpos($machineName, "\\")) . '\install';
            if (!function_exists($installFunction)) {
                $message = "Cannot install, $installFunction not found, ";
                $message .= "please ensure the correct namespacing in $installFilePath.";
                throw new ApiException(
                    $message,
                    0,
                    'oops'
                );
            }
            $installFunction();

            $info = $this->composerHelper->getInfo($machineName);
            if (!$info) {
                $message = "$machineName installed, but could not get the version of the namespace from composer.lock.";
                $message .= "installed_version table not updated.";
                throw new ApiException(
                    $message,
                    0,
                    'oops'
                );
            }
            $version = $info['version'];
            $updateFilePath = dirname($allModules[$machineName]['path']) . '/update.php';
            $updateFunctions = $this->getAllUpdateFunctions($updateFilePath, $machineName);
            if (!empty($updateFunctions)) {
                $version = array_pop($updateFunctions);
            }
            $installedVersion = new InstalledVersion(null, $machineName, $version);
            $this->installedVersionMapper->save($installedVersion);
        }

        return $modules;
    }

    /**
     * Uninstall one or more modules.
     *
     * @param array $modules Array of machine_names.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function uninstall(array $modules): array
    {
        if (empty($modules)) {
            throw new ApiException('No module machine_name given.', 6, 'oops');
        }
        $moduleDetails = $this->getModules();
        $composerLock = $this->settings->__get(['api', 'base_path']) . 'composer.lock';
        $this->composerHelper->parseComposerLock($composerLock);

        foreach ($modules as $machineName) {
            if (!isset($moduleDetails[$machineName])) {
                throw new ApiException("$machineName does not exist in the codebase.", 6, 'oops');
            }

            if ($this->getInstalledVersion($machineName) === false) {
                throw new ApiException("$machineName not installed.", 6, 'oops');
            }

            $uninstallFilePath = dirname($moduleDetails[$machineName]['path']) . '/install.php';
            if (!file_exists($uninstallFilePath)) {
                throw new ApiException("Cannot uninstall, $uninstallFilePath not found.", 0, 'oops');
            }

            include_once $uninstallFilePath;
            $func = substr($machineName, 0, strrpos($machineName, "\\")) . '\uninstall';
            if (!function_exists($func)) {
                throw new ApiException(
                    "Cannot install, $func not found, please ensure the correct namespacing in $uninstallFilePath.",
                    0,
                    'oops'
                );
            }
            $func();

            $installedVersion = $this->installedVersionMapper->findByModule($machineName);
            $this->installedVersionMapper->delete($installedVersion);
        }

        return $modules;
    }

    /**
     * Run updates for a module or plugin.
     *
     * @param array $modules Array of machine_names.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function update(array $modules): array
    {
        if (empty($modules)) {
            throw new ApiException('No module machine_name given.', 6, 'oops');
        }
        $allModules = $this->getModules();
        $updatesRan = [];
        foreach ($modules as $module) {
            if (!isset($allModules[$module])) {
                throw new ApiException("$module is not installed, cannot update");
            }
            $updates = $allModules[$module]['update_functions'];
            require_once dirname($allModules[$module]['path']) . '/update.php';
            foreach ($updates as $function => $version) {
                $function();
                $updatesRan[$module][$function] = $version;
                $installedVersion = $this->installedVersionMapper->findByModule($module);
                $installedVersion->setVersion($version);
                $this->installedVersionMapper->save($installedVersion);
            }
        }

        return $updatesRan;
    }

    /**
     * Return the default details attributed from a class, and its full filepath.
     *
     * @param string $className The classname.
     *
     * @return array|false
     *
     * @throws ApiException
     */
    protected function getDetails(string $className)
    {
        try {
            $reflector = new ReflectionClass($this->processorHelper->getProcessorString($className));
        } catch (ReflectionException $e) {
            throw new ApiException($e->getMessage(), 0, 'oops');
        }

        if (!$reflector->isAbstract()) {
            $properties = $reflector->getDefaultProperties();
            $details = $properties['details'] ?? false;
            if ($details === false) {
                return false;
            }
            $path = $reflector->getFileName();
            $installFilePath = dirname($path) . '/install.php';
            $installedVersion = $this->getInstalledVersion($details['machineName']);
            $installed = $installedVersion !== false ? $installedVersion->getVersion() : false;
            $installable = $this->functionExistsInFile($installFilePath, 'install');
            return [
                'details' => $details,
                'path' => $path,
                'installed' => $installed,
                'installable' => $installable,
            ];
        }

        return false;
    }

    /**
     * Return a sorted array of all update functions in a file.
     *
     * The Array is sorted by version string.
     * All functions should have the same namespace as the module.
     *
     * @param string $filePath Absolute oath to the update file.
     * @param string $className Namespaced classname for the module.
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getAllUpdateFunctions(string $filePath, string $className): array
    {
        if (!file_exists($filePath)) {
            return [];
        }
        include_once $filePath;
        $allFunctions = Utilities::getDefinedFunctionsInFile($filePath);
        if (empty($allFunctions)) {
            return [];
        }

        $phpDocFactory = new PhpDocFactory();
        $namespace = $className;
        $namespace = substr($namespace, -1) != "\\"
            ? substr($namespace, 0, strrpos($namespace, "\\") + 1)
            : $namespace;
        $updateFunctions = [];
        foreach ($allFunctions as $function) {
            $namespacedFunction = $namespace . $function;
            try {
                $docblock = $phpDocFactory->getFunctionDoc($namespacedFunction);
            } catch (PhpDocException | CacheException $e) {
                throw new ApiException($e->getMessage());
            }
            if (!$docblock->hasTag('version')) {
                throw new ApiException("Skipping $namespacedFunction: No version found in the PHPDoc");
            }
            $version = $docblock->getTag('version')[0]->getValue();
            if (!preg_match("/([vV])+\\s?([0-9]\\.){2}[0-9]/", $version)) {
                echo "Error: Invalid version found in the PHPDoc in $namespacedFunction\n";
                exit;
            }
            $version = trim(strtolower($version), 'dev-');
            $version = trim($version, 'v');
            $updateFunctions[$namespacedFunction] = $version;
        }

        if (!uasort($updateFunctions, [$this, 'sortByVersion'])) {
            echo "Error: Failed to sort the update functions\n";
            exit;
        }

        return $updateFunctions;
    }

    /**
     * Get list of update functions that need to be run for a module.
     *
     * @param string $filePath
     * @param string $className
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getUpdateFunctions(string $filePath, string $className): array
    {
        $allFunctions = $this->getAllUpdateFunctions($filePath, $className);
        if (empty($allFunctions)) {
            return [];
        }

        $installedVersion = $this->installedVersionMapper->findByModule($className);
        $currentVersion = $installedVersion->getVersion();
        $updates = [];
        foreach ($allFunctions as $function => $version) {
            if (!empty($currentVersion) && $this->sortByVersion($currentVersion, $version) >= 0) {
                continue;
            }
            $updates[$function] = $version;
        }

        return $updates;
    }

    /**
     * Validate if a function definition exists in a file.
     *
     * @param string $filePath Full filepath.
     * @param string $functionName Function name.
     *
     * @return bool
     */
    protected function functionExistsInFile(string $filePath, string $functionName): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $functions = Utilities::getDefinedFunctionsInFile($filePath);
        return in_array($functionName, $functions);
    }

    /**
     * Return false if a module or processor has not been installed, or its installed_version.
     *
     * @param string $module Module machine_name.
     *
     * @return false|InstalledVersion
     *
     * @throws ApiException
     */
    protected function getInstalledVersion(string $module)
    {
        $installedVersion = $this->installedVersionMapper->findByModule($module);
        if (empty($installedVersion->getMid())) {
            return false;
        }
        return $installedVersion;
    }
}
