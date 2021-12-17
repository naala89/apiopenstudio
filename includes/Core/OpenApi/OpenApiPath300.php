<?php

/**
 * Class OpenApiPath300.
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
 * Class to generate default elements for OpenApi v3.0.0.
 */
class OpenApiPath300 extends OpenApiPathAbstract
{
    /**
     * {@inheritDoc}
     */
    public function setDefault(Resource $resource)
    {
        $meta = $resource->getMeta();
        $uri = $resource->getUri();
        $path = "/$uri";
        $method = $resource->getMethod();
        $definition = [
            $path => [
                $method => [
                    'summary' => $resource->getName(),
                    'description' => $resource->getDescription(),
                    'tags' => [$uri],
                    'responses' => [
                        '200' => [
                            'description' => 'success',
                        ],
                        '400' => [
                            '$ref' => '#/components/responses/GeneralError',
                        ],
                        '401' => [
                            '$ref' => '#/components/responses/Unauthorised',
                        ],
                        '403' => [
                            '$ref' => '#/components/responses/Forbidden',
                        ],
                    ]
                ],
            ],
        ];
        $getParameters = !empty($meta) ? (array) $this->defaultGetParameters(json_decode($meta, true)) : [];
        if (!empty($getParameters)) {
            if (!isset($definition[$path][$method]['parameters'])) {
                $definition[$path][$method]['parameters'] = [];
            }
            foreach ($getParameters as $getParameter) {
                $definition[$path][$method]['parameters'][] = $getParameter;
            }
        }
        $pathParameters = !empty($meta) ? (array) $this->defaultPathParameters(json_decode($meta, true)) : [];
        if (!empty($pathParameters)) {
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
        $postParameters = !empty($meta) ? (array) $this->defaultPostParameters(json_decode($meta, true)) : [];
        if (!empty($postParameters)) {
            $definition[$path][$method]['requestBody'] = $postParameters;
        }

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    protected function defaultGetParameters(array $meta)
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
            $parameter->schema = new stdClass();
            if (!isset($item['expected_type'])) {
                $parameter->schema->type = 'string';
            } else {
                switch ($item['expected_type']) {
                    case 'boolean':
                        $parameter->schema->type = 'boolean';
                        break;
                    case 'integer':
                        $parameter->schema->type = 'integer';
                        break;
                    case 'float':
                        $parameter->schema->type = 'float';
                        break;
                    case 'text':
                    case 'json':
                    case 'xml':
                    case 'html':
                    case 'empty':
                        $parameter->schema->type = 'string';
                        break;
                    case 'array':
                        $parameter->schema->type = 'array';
                        $parameter->schema->items->type = 'string';
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
    protected function defaultPathParameters(array $meta)
    {
        $parameters = [];
        $items = $this->findProcessors('var_uri', $meta);
        foreach ($items as $item) {
            $parameter = new stdClass();
            $parameter->in = 'path';
            $parameter->name = 'pathVar' . $item['index'];
            $parameter->required = true;

            if (!isset($item['expected_type'])) {
                $parameter->schema->type = 'string';
            } else {
                switch ($item['expected_type']) {
                    case 'boolean':
                        $parameter->schema->type = 'boolean';
                        break;
                    case 'integer':
                        $parameter->schema->type = 'integer';
                        break;
                    case 'float':
                        $parameter->schema->type = 'float';
                        break;
                    case 'text':
                    case 'json':
                    case 'xml':
                    case 'html':
                    case 'empty':
                        $parameter->schema->type = 'string';
                        break;
                    case 'array':
                        $parameter->schema->type = 'array';
                        $parameter->schema->items->type = 'string';
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
    protected function defaultPostParameters(array $meta)
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
            if (!isset($item['expected_type'])) {
                $parameters->{$content}->schema->properties->{$item['key']}->type = 'string';
            } else {
                switch ($item['expected_type']) {
                    case 'boolean':
                        $parameters->{$content}->schema->properties->{$item['key']}->type = 'boolean';
                        break;
                    case 'integer':
                        $parameters->{$content}->schema->properties->{$item['key']}->type = 'integer';
                        $parameters->{$content}->schema->properties->{$item['key']}->format = 'int64';
                        break;
                    case 'float':
                        $parameters->{$content}->schema->properties->{$item['key']}->type = 'float';
                        $parameters->{$content}->schema->properties->{$item['key']}->format = 'float64';
                        break;
                    case 'array':
                        $parameters->{$content}->schema->properties->{$item['key']}->type = 'array';
                        $parameters->{$content}->schema->properties->{$item['key']}->items = new stdClass();
                        $parameters->{$content}->schema->properties->{$item['key']}->items->type = 'string';
                        break;
                    default:
                        $parameters->{$content}->schema->properties->{$item['key']}->type = 'string';
                        break;
                }
            }
        }

        return $parameters;
    }
}
