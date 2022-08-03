<?php

namespace ApiOpenStudio\Core;

use ADOConnection;
use ApiOpenStudio\Db\InstalledVersion;
use ApiOpenStudio\Db\InstalledVersionMapper;
use Berlioz\PhpDoc\Exception\PhpDocException;
use Berlioz\PhpDoc\PhpDocFactory;
use Composer\InstalledVersions;
use Psr\SimpleCache\CacheException;
use ReflectionClass;
use ReflectionException;
use ApiOpenStudio\Core\Utilities;

class ModuleHelper
{
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
     * Return a list of non-core modules and plugins in the codebase, and their details array.
     *
     * @return array
     *
     * @throws ApiException
     */
    public function getModules(): array
    {
        $modules = [];

        $basePath = $this->settings->__get(['api', 'base_path']) . 'includes/';
        foreach ($this->directories as $directory) {
            $classNames = $this->listClassesInDirectory->listClassesInDirectory($basePath . $directory);
            foreach ($classNames as $className) {
                $details = $this->getDetails($className);
                if ($details !== false && isset($details['details']['machineName'])) {
                    $modules[$details['details']['machineName']] = $details;
                }
            }
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
        $installedModules = [];

        $installedVersions = $this->installedVersionMapper->findAll();
        foreach ($installedVersions as $installedVersion) {
            $row = $installedVersion->dump();
            $installedModules[$row['module']] = $row['version'];
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
        $modules = $this->getModules();
        $installed = $this->getInstalled();
        $uninstalled = [];

        foreach ($modules as $moduleMachineName => $details) {
            $isInstalled = false;
            foreach (array_keys($installed) as $installedMachineName) {
                if ($installedMachineName == $moduleMachineName) {
                    $isInstalled = true;
                }
            }
            $installFilePath = dirname($details['path']) . '/install.php';
            if (!$isInstalled && $this->functionExistsInFile($installFilePath, 'install')) {
                $uninstalled[] = $moduleMachineName;
            }
        }

        return $uninstalled;
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
            throw new ApiException('No module machine_name given.', 6, 'oops');
        }

        $moduleDetails = $this->getModules();
        $composerLock = $this->settings->__get(['api', 'base_path']) . 'composer.lock';
        $this->composerHelper->parseComposerLock($composerLock);

        foreach ($modules as $machineName) {
            if (!isset($moduleDetails[$machineName])) {
                throw new ApiException("$machineName does not exist in the codebase.", 6, 'oops');
            }

            if ($this->isInstalled($machineName)) {
                throw new ApiException("$machineName already installed.", 6, 'oops');
            }

            $installFilePath = dirname($moduleDetails[$machineName]['path']) . '/install.php';
            if (!file_exists($installFilePath)) {
                throw new ApiException("$installFilePath is not defined, cannot install.", 6, 'oops');
            }

            include_once($installFilePath);
            $namespace = substr($machineName, 0, strrpos($machineName, "\\"));
            $func = $namespace . "\install";
            if (!function_exists($func)) {
                throw new ApiException("$func() is not defined, no need to install.", 6, 'oops');
            }
            $func();

            $info = $this->composerHelper->getInfo($machineName);
            if (!$info) {
                throw new ApiException('Could not get the version of the namespace from composer.lock');
            }
            $installedVersion = new InstalledVersion(null, $machineName, $info['version']);
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

            if (!$this->isInstalled($machineName)) {
                throw new ApiException("$machineName not installed.", 6, 'oops');
            }

            $installFilePath = dirname($moduleDetails[$machineName]['path']) . '/install.php';
            if (!file_exists($installFilePath)) {
                throw new ApiException("$installFilePath is not defined, cannot uninstall.", 6, 'oops');
            }

            include_once($installFilePath);
            $namespace = substr($machineName, 0, strrpos($machineName, "\\"));
            $func = $namespace . "\uninstall";
            if (!function_exists($func)) {
                throw new ApiException("$func() is not defined, no need to uninstall.", 6, 'oops');
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
        $moduleDetails = $this->getModules();

        $updates = [];
        foreach ($modules as $machineName) {
            if (!isset($moduleDetails[$machineName])) {
                throw new ApiException("$machineName does not exist in the codebase.", 6, 'oops');
            }

            if (!$this->isInstalled($machineName)) {
                throw new ApiException("$machineName not installed.", 6, 'oops');
            }

            $updateFilePath = dirname($moduleDetails[$machineName]['path']) . '/update.php';
            if (!file_exists($updateFilePath)) {
                throw new ApiException("$updateFilePath is not defined, cannot update.", 6, 'oops');
            }

            include_once($updateFilePath);

            $phpDocFactory = new PhpDocFactory();
            $allFunctions = Utilities::getDefinedFunctionsInFile($updateFilePath);
            $functions = [];
            $namespace = $machineName;
            $namespace = substr($namespace, -1) != "\\"
                ? substr($namespace, 0, strrpos($namespace, "\\") + 1)
                : $namespace;
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
                if (!is_numeric($version)) {
                    throw new ApiException("Error: Invalid version found in the PHPDoc in $namespacedFunction");
                }
                $functions[$version] = $namespacedFunction;
            }
            ksort($functions);

            $installedVersion = $this->installedVersionMapper->findByModule($machineName);
            $currentVersion = $installedVersion->getUpdate();
            foreach ($functions as $version => $function) {
                if (!empty($currentVersion) && $version <= $currentVersion) {
                    continue;
                }
                $function();
                $updates[$machineName][] = $function;
            }
            $installedVersion->setUpdate($version);
            $this->installedVersionMapper->save($installedVersion);
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
        foreach ($functions as $function) {
            if ($function == $functionName) {
                return true;
            }
        }
        return false;
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
            if ($details === false || strpos($details['machineName'], "\\") === false) {
                return false;
            }
            return [
                'details' => $details,
                'path' => $reflector->getFileName(),
            ];
        }

        return false;
    }

    /**
     * Return if a module or processor has been installed.
     *
     * @param string $machineName Module machine_name.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function isInstalled(string $machineName): bool
    {
        $installedVersion = $this->installedVersionMapper->findByModule($machineName);
        return !empty($installedVersion->getMid());
    }
}
