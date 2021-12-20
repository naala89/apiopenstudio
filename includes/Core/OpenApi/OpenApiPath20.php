<?php

/**
 * Class OpenApiPath20.
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

use ApiOpenStudio\Db\Resource;
use stdClass;

/**
 * Class to generate default path elements for OpenApi v2.0.
 */
class OpenApiPath20 extends OpenApiPathAbstract
{
    /**
     * {@inheritDoc}
     */
    public function setDefault(Resource $resource)
    {
        $path = '/' . $resource->getUri();
        $method = $resource->getMethod();
        $meta = $resource->getMeta();
        $definition = [
            $path => [
                $method => [
                    'description' => $resource->getDescription(),
                    'summary' => $resource->getName(),
                    'tags' => [$path],
                    'produces' => [
                        'application/json',
                        'application/xml',
                        'application/text',
                        'text/html',
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'success response',
                        ],
                        '400' => [
                            '$ref' => '#/responses/GeneralError'
                        ],
                        '401' => [
                            '$ref' => '#/responses/Unauthorised'
                        ],
                        '403' => [
                            '$ref' => '#/responses/Forbidden'
                        ],
                    ],
                ],
            ],
        ];
        $getParameters = !empty($meta) ? $this->defaultGetParameters(json_decode($meta)) : [];
        if (!empty((array) $getParameters)) {
            if (!isset($definition[$path][$method]['parameters'])) {
                $definition[$path][$method]['parameters'] = [];
            }
            foreach ($getParameters as $getParameter) {
                $definition[$path][$method]['parameters'][] = $getParameter;
            }
        }
        $pathParameters = !empty($meta) ? $this->defaultPathParameters(json_decode($meta)) : [];
        if (!empty((array) $pathParameters)) {
            if (!isset($definition[$path][$method]['parameters'])) {
                $definition[$path][$method]['parameters'] = [];
            }
            foreach ($pathParameters as $pathParameter) {
                $definition[$path][$method]['parameters'][] = $pathParameter;
                $newPath = $path . '/{' . $pathParameter->name . '}';
                $definition[$newPath] = $definition[$path];
                unset($definition[$path]);
                $path = $newPath;
            }
        }
        $postParameters = !empty($meta) ? $this->defaultPostParameters(json_decode($meta)) : [];
        if (!empty((array) $postParameters)) {
            $definition[$path][$method]['requestBody'] = $postParameters;
        }

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    protected function defaultGetParameters(stdClass $meta)
    {
        $parameters = [];
        $count = 1;
        $items = $this->findProcessors('var_get', $meta);
        foreach ($items as $item) {
            $parameter = new stdClass();
            $parameter->in = 'query';
            if (!isset($item['key'])) {
                $parameter->name = 'key' . $count++;
            } else {
                $parameter->name = $item['key'];
            }
            if (!isset($item['nullable'])) {
                $parameter->required = true;
            } else {
                $parameter->required = !((bool) $item['nullable']);
            }
            if (!isset($item['expected_type'])) {
                $parameter->type = 'string';
            } else {
                switch ($item['expected_type']) {
                    case 'boolean':
                        $parameter->type = 'boolean';
                        break;
                    case 'integer':
                        $parameter->type = 'number';
                        break;
                    case 'float':
                        $parameter->type = 'float';
                        break;
                    case 'array':
                        $parameter->type = 'array';
                        $parameter->items->type = 'string';
                        break;
                    default:
                        $parameter->type = 'string';
                        break;
                }
            }
            $parameters[] = $parameter;
        }

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    protected function defaultPathParameters(stdClass $meta)
    {
        $parameters = [];
        $items = $this->findProcessors('var_uri', $meta);
        foreach ($items as $item) {
            $parameter = new stdClass();
            $parameter->in = 'path';
            $parameter->name = 'pathVar' . $item['index'];
            $parameter->required = true;

            if (!isset($item['expected_type'])) {
                $parameter->type = 'string';
            } else {
                switch ($item['expected_type']) {
                    case 'boolean':
                        $parameter->type = 'boolean';
                        break;
                    case 'integer':
                        $parameter->type = 'integer';
                        break;
                    case 'float':
                        $parameter->type = 'number';
                        break;
                    case 'text':
                    case 'json':
                    case 'xml':
                    case 'html':
                    case 'empty':
                        $parameter->type = 'string';
                        break;
                    case 'array':
                        $parameter->type = 'array';
                        $parameter->items->type = 'string';
                        break;
                }
            }
            $parameters[$item['index']] = $parameter;
        }

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    protected function defaultPostParameters(stdClass $meta)
    {
        $content = "application/json";
        $count = 1;
        $parameters = new stdClass();

        $items = $this->findProcessors('var_post', $meta);
        foreach ($items as $item) {
            if (!isset($parameters->{$content})) {
                $parameters->{$content} = new stdClass();
                $parameters->{$content}->schema = new stdClass();
                $parameters->{$content}->schema->type = 'object';
                $parameters->{$content}->schema->properties = new stdClass();
            }
            if (!isset($item['key'])) {
                $item['key'] = 'postParam' . $count++;
            }
            $parameters->{$content}->schema->properties->{$item['key']} = new stdClass();
            switch ($item['expected_type']) {
                case 'boolean':
                    $parameters->{$content}->schema->properties->{$item['key']}->type = 'boolean';
                    break;
                case 'integer':
                    $parameters->{$content}->schema->properties->{$item['key']}->type = 'integer';
                    break;
                case 'float':
                    $parameters->{$content}->schema->properties->{$item['key']}->type = 'number';
                    break;
                case 'text':
                case 'json':
                case 'xml':
                case 'html':
                case 'empty':
                    $parameters->{$content}->schema->properties->{$item['key']}->type = 'string';
                    break;
                case 'array':
                    $parameters->{$content}->schema->properties->{$item['key']}->type = 'array';
                    $parameters->{$content}->schema->properties->{$item['key']}->items = new stdClass();
                    $parameters->{$content}->schema->properties->{$item['key']}->items->type = 'string';
                    break;
            }
        }

        return $parameters;
    }
}
