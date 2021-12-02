<?php

/**
 * Abstract class OpenApiParentAbstract.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core\OpenApi;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;

/**
 * Abstract class to generate default parent elements for OpenApi.
 */
abstract class OpenApiParentAbstract
{
    /**
     * @var array Doc definition.
     */
    protected array $definition = [];

    /**
     * @var Config
     *   Settings object.
     */
    protected Config $settings;

    public function __construct()
    {
        $this->settings = new Config();
    }

    /**
     * Import an existing definition.
     *
     * @param array|string $definition
     *
     * @throws ApiException
     */
    public function import($definition)
    {
        if (is_string($definition)) {
            $definition = json_decode($definition, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('invalid input JSON string');
            }
        }
        $this->definition = $definition;
    }

    /**
     * Export the definition.
     *
     * @param bool $encoded
     *
     * @return array|string
     *
     * @throws ApiException
     */
    public function export(bool $encoded = true)
    {
        if ($encoded) {
            if (!$result = json_encode($this->definition, true)) {
                throw new ApiException('failed to encode the JSON array');
            }
            return $result;
        }
        return $this->definition;
    }

    /**
     * Sets the default OpenApi parent schema fragments to define the resources in the doc (application).
     *
     * @param string $accountName
     * @param string $applicationName
     *
     * @throws ApiException
     */
    abstract public function setDefault(string $accountName, string $applicationName);

    /**
     * Set the account name.
     *
     * @param string $accountName
     */
    abstract public function setAccount(string $accountName);

    /**
     * Set the application name.
     *
     * @param string $applicationName
     */
    abstract public function setApplication(string $applicationName);

    /**
     * Set the domain.
     *
     * @throws ApiException
     */
    abstract public function setDomain();
}
