<?php

/**
 * Abstract class OpenApiParentAbstract.
 *
 * @package    ApiOpenStudio\Core
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
use stdClass;

/**
 * Abstract class to generate default parent elements for OpenApi.
 */
abstract class OpenApiParentAbstract
{
    /**
     * @var stdClass Doc definition.
     */
    protected stdClass $definition;

    /**
     * @var Config
     *   Settings object.
     */
    protected Config $settings;

    /**
     * @param Config|null $settings
     */
    public function __construct(Config $settings = null)
    {
        $this->settings = empty($settings) ? (new Config()) : $settings;
        $this->definition = new stdClass();
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
            $definition = json_decode($definition);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('invalid input JSON string', 4, -1, 400);
            }
        }
        $this->definition = $definition;
    }

    /**
     * Export the definition.
     *
     * @param bool $encoded JSON encoded.
     *
     * @return stdClass|string
     *
     * @throws ApiException
     */
    public function export(bool $encoded = true)
    {
        $result = $this->definition;
        if ($encoded) {
            if (!$result = json_encode($result, JSON_UNESCAPED_SLASHES)) {
                throw new ApiException('failed to encode the JSON array');
            }
        }
        return $result;
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
     * Get the account name from the schema
     *
     * @return string
     *
     * @throws ApiException
     */
    abstract public function getAccount(): string;

    /**
     * Get the application name from the schema
     *
     * @return string
     *
     * @throws ApiException
     */
    abstract public function getApplication(): string;

    /**
     * Set the account name.
     *
     * @param string $accountName
     *
     * @throws ApiException
     */
    abstract public function setAccount(string $accountName);

    /**
     * Set the application name.
     *
     * @param string $applicationName
     *
     * @throws ApiException
     */
    abstract public function setApplication(string $applicationName);

    /**
     * Set the domain.
     *
     * @throws ApiException
     */
    abstract public function setDomain();
}
