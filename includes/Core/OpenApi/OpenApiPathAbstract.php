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
use ApiOpenStudio\Core\ProcessorHelper;
use ApiOpenStudio\Db\Resource;
use stdClass;

/**
 * Abstract class to generate default path elements for OpenApi.
 */
abstract class OpenApiPathAbstract
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
     * @var ProcessorHelper
     */
    protected ProcessorHelper $processorHelper;

    public function __construct()
    {
        $this->settings = new Config();
        $this->definition = new stdClass();
        $this->processorHelper = new ProcessorHelper();
    }

    /**
     * Sets the OpenApi path schema fragment to define the resources in the doc (application).
     *
     * @param Resource $resource
     *
     * @throws ApiException
     */
    abstract public function setDefault(Resource $resource);

    /**
     * Return the default GET parameters OpenApi object.
     *
     * @param stdClass $meta
     *
     * @return array|stdClass
     */
    abstract protected function defaultGetParameters(stdClass $meta);

    /**
     * Return the default POST parameters OpenApi object.
     *
     * @param stdClass $meta
     *
     * @return array|stdClass
     */
    abstract protected function defaultPostParameters(stdClass $meta);

    /**
     * Return the default PATH parameters OpenApi object.
     *
     * @param stdClass $meta
     *
     * @return array|stdClass
     */
    abstract protected function defaultPathParameters(stdClass $meta);

    /**
     * Import an existing definition.
     *
     * @param stdClass|string $definition
     *
     * @throws ApiException
     */
    public function import($definition)
    {
        if (is_string($definition)) {
            $definition = json_decode($definition);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('invalid input JSON string');
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
            if (!$result = json_encode($this->definition, JSON_UNESCAPED_SLASHES)) {
                throw new ApiException('failed to encode the JSON array');
            }
        }
        return $result;
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

        if (!isset($this->definition->{$uri})) {
            $keys = array_keys((array) $this->definition);
            if (sizeof($keys) > 1) {
                throw new ApiException('this resource has too many uri definitions - there should only be 1');
            }
            if (sizeof($keys < 1)) {
                $this->definition = $this->setDefault($resource);
            } else {
                $this->definition->{$uri} = $this->definition->{$keys[0]};
                unset($this->definition->{$keys[0]});
            }
        }

        if (!isset($this->definition->{$uri}->{$method})) {
            $keys = array_keys((array) $this->definition->{$uri});
            if (sizeof($keys) > 1) {
                throw new ApiException('this resource has too many method definitions - there should only be 1');
            }
            if (sizeof($keys < 1)) {
                $this->definition = $this->setDefault($resource);
            } else {
                $this->definition->{$uri}->{$method} = $this->definition->{$uri}->{$keys[0]};
                unset($this->definition->{$uri}->{$keys[0]});
            }
        }

        $this->definition->{$uri}->{$method}->summary = $name;
        $this->definition->{$uri}->{$method}->description = $description;
    }

    /**
     * Return an array of all instances of a processor in metadata.
     *
     * @param string $machineName
     * @param stdClass $meta
     *
     * @return array
     */
    protected function findProcessors(string $machineName, stdClass $meta): array
    {
        $result = [];

        $keys = array_keys((array) $meta);
        foreach ($keys as $key) {
            $value = $meta->{$key};
            if ($this->processorHelper->isProcessor($value)) {
                $result = array_merge($result, $this->findProcessors($machineName, $value));
            } elseif (is_array($value)) {
                foreach ($value as $val) {
                    $result = array_merge($result, $this->findProcessors($machineName, $val));
                }
            } elseif ($key == 'processor' && $value == $machineName) {
                $item = [];
                $processorKeys = array_keys((array) $meta);
                foreach ($processorKeys as $processorKey) {
                    if (!is_object($meta->{$processorKey})) {
                        $item[$processorKey] = $meta->{$processorKey};
                    }
                }
                $result[] = $item;
            }
        }

        return $result;
    }
}
