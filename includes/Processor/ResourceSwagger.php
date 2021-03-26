<?php

/**
 * Class ResourceSwagger.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db\ResourceMapper;

/**
 * Class ResourceSwagger
 *
 * Processor class to create resource stubs from a swagger file.
 */
class ResourceSwagger extends ResourceBase
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
    'name' => 'Import Swagger',
        'machineName' => 'resourceSwagger',
        'description' => 'Create a custom API resource using a Swagger YAML document.',
        'menu' => 'Admin',
        'input' => [
            'resource' => [
                'description' => 'The resource string or file. This can be an attached file or a urlencoded GET var.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'file'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * Parameter count.
     *
     * @var integer
     */
    private $paramCount;

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $this->paramCount = 2;
        $resources = array();
        $swagger = $this->importData();

        if (empty($swagger['paths'])) {
            throw new Core\ApiException('Missing paths element in swagger YAML', 1);
        }

        foreach ($swagger['paths'] as $path => $methods) {
            $pathParts = explode('/', trim($path, '/'));

            if (sizeof($pathParts) < 2) {
                throw new Core\ApiException('invalid path (must be at least noun/verb): ' . $path, 1);
            }

            $uriParams = array();
            if (sizeof($pathParts) > 2) {
                $uriParams = $this->extractUriParams(array_slice($pathParts, 2));
            }
            $noun = $pathParts[0];
            $verb = $pathParts[1];

            foreach ($methods as $method => $definition) {
                $requestVars = $this->extractParameters($definition['parameters'], $method);

                $resource = array();
                $resource['name'] = !empty($definition['operationId']) ? $definition['operationId'] : 'noName';
                $resource['description'] = !empty($definition['description'])
                    ? $definition['description'] : 'noDescription';
                $resource['uri']['noun'] = $noun;
                $resource['uri']['verb'] = $verb;
                $resource['method'] = $method;
                $resource['security'] = array(
                'processor' => 'tokenConsumer',
                'meta' => array(
                'id' => 1,
                'token' => array(
                'processor' => 'varGet',
                'meta' => array(
                'id' => 2,
                'name' => 'token'
                )
                )
                )
                );
                $resource['process'] = 'true';
                $resource['fragments'] = array();
                $resource['fragments'] = array_merge($resource['fragments'], $uriParams);
                $resource['fragments'] = array_merge($resource['fragments'], $requestVars);

                $this->save($resource);

                $resources[] = array(
                'uri' => $resource['uri'],
                'method' => $method,
                'appId' => $this->request->getAppId()
                );
            }
        }

        return $resources;
    }

    /**
     * Create or update a resource from YAML.
     * The Yaml is either post string 'yaml', or file 'yaml'.
     * File takes precedence over the string if both present.
     *
     * @param mixed $data String or filename of YAML.
     *
     * @return boolean
     *
     * @throws Core\ApiException Error.
     */
    protected function save($data)
    {
        $this->validateData($data);

        $name = $data['name'];
        $description = $data['description'];
        $method = $data['method'];
        $identifier = strtolower($data['uri']['noun']) . strtolower($data['uri']['verb']);
        $meta = array();
        $meta['security'] = $data['security'];
        $meta['process'] =  $data['process'];
        $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

        $mapper = new ResourceMapper($this->db);
        $resource = $mapper->findByAppIdMethodIdentifier($this->request->getAppId(), $method, $identifier);
        if (empty($resource->getId())) {
            $resource->setAppId($this->request->getAppId());
            $resource->setMethod($method);
            $resource->setIdentifier($identifier);
        }
        $resource->setName($name);
        $resource->setDescription($description);
        $resource->setMeta(json_encode($meta));
        $resource->setTtl($ttl);
        return $mapper->save($resource);
    }

    /**
     * Extract YAML data.
     *
     * @param mixed $data YAML string.
     *
     * @return array|string
     */
    protected function importData($data)
    {
        return \Spyc::YAMLLoadString($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Input data.
     *
     * @return mixed
     */
    protected function exportData($data)
    {
    }

    /**
     * Extract URI parameters.
     *
     * @param array $uriParams Array of URI parameters.
     *
     * @return array
     *
     * @throws \ApiOpenStudio\Core\ApiException Error.
     */
    protected function extractUriParams(array $uriParams)
    {
        $result = array();
        foreach ($uriParams as $key => $val) {
            if (!preg_match("/^\{[a-z0-9_-]*\}$/i", $val)) {
                throw new Core\ApiException("invalid URI element: $val", 1);
            }
            $result[] = array(
            'processor' => 'varUri',
            'meta' => array(
            'id' => $this->paramCount++,
            'index' => $key)
            );
        }
        return $result;
    }

    /**
     * Extract parameters.
     *
     * @param array $parameters Array of parameters.
     * @param string $method Resource method.
     *
     * @return array
     *
     * @throws Core\ApiException Error.
     */
    protected function extractParameters(array $parameters, string $method)
    {
        $result = [];
        foreach ($parameters as $parameter) {
            $p = [];
            $parameterCount = 1;
            switch ($parameter['in']) {
                case 'query':
                    $p['processor'] = $method == 'get' ? 'varGet' : 'varPost';
                    $p['meta']['id'] = $this->paramCount++;
                    $p['meta']['name'] = $parameter['name'];
                    break;
                case 'body':
                    $p['processor'] = 'varBody';
                    $p['meta']['id'] = $this->paramCount++;
                    break;
            }
            // strongly typed
            if (!empty($parameter['items']['type'])) {
                $this->logger->info('strongly typed: ' . $parameter['items']['type']);
                $p = [
                    'meta' => [
                        'id' => $this->paramCount++,
                        'value' => $p,
                    ],
                ];
                switch ($parameter['items']['type']) {
                    case 'boolean':
                        $p['processor'] = 'varBool';
                        break;
                    case 'float':
                        $p['processor'] = 'varFloat';
                        break;
                    case 'integer':
                        $p['processor'] = 'varInt';
                        break;
                    case 'number':
                        $p['processor'] = 'varNum';
                        break;
                    case 'text':
                        $p['processor'] = 'varStr';
                        break;
                    default:
                        throw new Core\ApiException('unknown type: ' . $parameter['items']['type'], 1);
                        break;
                }
            }
            $result[] = $p;
        }
        return $result;
    }
}
