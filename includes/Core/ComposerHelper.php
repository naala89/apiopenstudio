<?php

namespace ApiOpenStudio\Core;

class ComposerHelper
{
    /**
     * @var array|string[]
     */
    protected array $lockPackagesSections = [
        'packages',
        'packages-dev',
    ];

    /**
     * @var array
     */
    protected array $namespaceMap;

    /**
     * @var string
     */
    protected string $composerLockPath;

    /**
     * @param string $composerLockPath
     *
     * @throws ApiException
     */
    public function __construct(string $composerLockPath = '')
    {
        if (!empty($composerLockPath)) {
            $this->composerLockPath = $composerLockPath;
            $this->parseComposerLock();
        }
    }

    /**
     * Parse the `composer.lock` file to map namespaces to package names.
     *
     * @param string $composerLockPath
     *
     * @return void
     *
     * @throws ApiException
     */
    public function parseComposerLock(string $composerLockPath = '')
    {
        if (!empty($composerLockPath)) {
            $this->composerLockPath = $composerLockPath;
        }
        if (!file_exists($this->composerLockPath)) {
            throw new ApiException('Invalid or missing composer.lock path.');
        }

        $this->namespaceMap = [];
        $lockArray = json_decode(file_get_contents($this->composerLockPath), true);
        foreach ($this->lockPackagesSections as $lockPackagesSection) {
            $this->parseComposerLockSection($lockArray, $lockPackagesSection);
        }
    }

    /**
     * Get info for a namespace.
     *
     * @param string $namespace
     *
     * @return array|false
     */
    public function getInfo(string $namespace)
    {
        $info = false;
        $namespace = substr($namespace, 0, 1) == "\\" ? substr($namespace, 1) : $namespace;

        while (!empty($namespace) && $info === false) {
            $info = $this->getPackageInfo($namespace);
            $namespace = substr($namespace, -1, 1) == "\\" ? substr($namespace, 0, -1) : $namespace;
            $namespace = strrpos($namespace, "\\") !== false
                ? substr($namespace, 0, strrpos($namespace, "\\") + 1)
                : '';
        }

        return $info;
    }

    /**
     * Try to get info for a namespace.
     *
     * @param string $namespace
     *
     * @return false|array
     */
    protected function getPackageInfo(string $namespace)
    {
        return $this->namespaceMap[$namespace] ?? false;
    }

    /**
     * Parse a `composer.lock` section to map namespaces to package names.
     *
     * @param array $lockArray
     * @param string $lockPackagesSection
     *
     * @return void
     */
    protected function parseComposerLockSection(array $lockArray, string $lockPackagesSection)
    {
        foreach ($lockArray[$lockPackagesSection] as $package) {
            $packageName = $package['name'];
            $packageVersion = $package['version'];
            if (isset($package['autoload'])) {
                $packageNamespaces = $this->parseAutoload($package['autoload']);
                foreach ($packageNamespaces as $namespace) {
                    $this->namespaceMap[$namespace] = [
                        'name' => $packageName,
                        'version' => $packageVersion,
                    ];
                }
            }
        }
    }

    /**
     * Return an array of namespace to package mappings for a package.
     *
     * @param array $autoload
     *
     * @return array
     */
    protected function parseAutoload(array $autoload): array
    {
        $result = [];
        if (isset($autoload['psr-4'])) {
            foreach (array_keys($autoload['psr-4']) as $namespace) {
                $result[] = $namespace;
            }
        }
        if (isset($autoload['psr-0'])) {
            foreach (array_keys($autoload['psr-0']) as $namespace) {
                $result[] = $namespace;
            }
        }
        return $result;
    }
}
