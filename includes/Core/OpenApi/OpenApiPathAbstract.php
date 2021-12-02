<?php

/**
 * Abstract class OpenApiPathAbstract.
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
use ApiOpenStudio\Db\Resource;

/**
 * Abstract class to generate default path elements for OpenApi.
 */
abstract class OpenApiPathAbstract
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
     * Sets the OpenApi path schema fragment to define the resources in the doc (application).
     *
     * @param Resource $resource
     *
     * @return array
     *
     * @throws ApiException
     */
    abstract public function setDefault(Resource $resource): array;

    /**
     * Import an existing definition.
     *
     * @param array $definition
     */
    public function import(array $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Export the definition.
     *
     * @return array
     */
    public function export(): array
    {
        return $this->definition;
    }

    /**
     * Update the OpenApi definition fragment for a path.
     *
     * @param Resource $resource
     *
     * @throws ApiException
     */
    public function update(Resource $resource)
    {
        $uri = $resource->getUri();
        $method = $resource->getMethod();
        $name = $resource->getName();
        $description = $resource->getDescription();

        if (!isset($this->definition[$uri])) {
            $keys = array_keys($this->definition);
            if (sizeof($keys) > 1) {
                throw new ApiException('this resource has too many uri definitions - there should only be 1');
            }
            if (sizeof($keys < 1)) {
                $this->definition = $this->setDefault($resource);
            } else {
                $this->definition[$uri] = $this->definition[$keys[0]];
                unset($this->definition[$keys[0]]);
            }
        }

        if (!isset($this->definition[$uri][$method])) {
            $keys = array_keys($this->definition[$uri]);
            if (sizeof($keys) > 1) {
                throw new ApiException('this resource has too many method definitions - there should only be 1');
            }
            if (sizeof($keys < 1)) {
                $this->definition = $this->setDefault($resource);
            } else {
                $this->definition[$uri][$method] = $this->definition[$uri][$keys[0]];
                unset($this->definition[$uri][$keys[0]]);
            }
        }

        $this->definition[$uri][$method]['summary'] = $name;

        $this->definition[$uri][$method]['description'] = $description;
    }
}
