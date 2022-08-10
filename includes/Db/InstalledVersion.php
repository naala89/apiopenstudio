<?php

/**
 * Class InstalledVersion.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

/**
 * Class InstalledVersion.
 *
 * DB class for storing installed_version data.
 */
class InstalledVersion
{
    /**
     * Module ID.
     *
     * @var int|null
     */
    protected ?int $mid;

    /**
     * Module name.
     *
     * @var string|null
     */
    protected ?string $module;

    /**
     * Module version.
     *
     * @var string|null
     */
    protected ?string $version;

    /**
     * @param int|null $mid
     * @param string|null $module
     * @param string|null $version
     */
    public function __construct(int $mid = null, string $module = null, string $version = null)
    {
        $this->mid = $mid;
        $this->module = $module;
        $this->version = $version;
    }

    /**
     * Get the module ID.
     *
     * @return int|null Module ID.
     */
    public function getMid(): ?int
    {
        return $this->mid;
    }

    /**
     * Set the module ID.
     *
     * @param int|null $mid Module ID.
     *
     * @return void
     */
    public function setMid(?int $mid)
    {
        $this->mid = $mid;
    }

    /**
     * Get the module name.
     *
     * @return string|null Module name.
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Set the module name.
     *
     * @param string|null $module Module name.
     *
     * @return void
     */
    public function setModule(?string $module)
    {
        $this->module = $module;
    }

    /**
     * Get the module version.
     *
     * @return string|null Module version.
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * Set the module version.
     *
     * @param string|null $version Module version.
     *
     * @return void
     */
    public function setVersion(?string $version)
    {
        $this->version = $version;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Account.
     */
    public function dump(): array
    {
        return [
            'mid' => $this->mid,
            'module' => $this->module,
            'version' => $this->version,
        ];
    }
}
