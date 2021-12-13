<?php

/**
 * Class OpenApiParent2_0.
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
use stdClass;

/**
 * Class to generate default parent elements for OpenApi v2.0.
 */
class OpenApiParent2_0 extends OpenApiParentAbstract
{
    /**
     * OpenApi doc version.
     */
    protected const VERSION = "2.0";

    /**
     * Returns the default info element.
     *
     * @param string $applicationName
     *
     * @return stdClass
     *
     * @throws ApiException
     */
    protected function getDefaultInfo(string $applicationName): stdClass
    {
        $info = [
            'title' => $applicationName,
            'description' => "This if the definitions for the $applicationName application.",
            'termsOfService' => 'https://www.apiopenstudio.com/license/',
            'contact' => [
                'name' => 'API Support',
                'email' => 'contact@' . $this->settings->__get(['api', 'url']),
            ],
            'license' => [
                'name' => '“ApiOpenStudio Public License” based on Mozilla Public License 2.0',
                'url' => 'https://www.apiopenstudio.com/license/',
            ],
            'version' => '1.0.0',
        ];

        return json_decode(json_encode($info, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default responses element.
     *
     * @return stdClass
     */
    protected function getDefaultResponses(): stdClass
    {
        $responses = [
            'GeneralError' => [
                'description' => 'General error.',
                'schema' => [
                    '$ref' => '#/definitions/GeneralError',
                ],
                'examples' => [
                    'application/json' => [
                        'error' => [
                            'id' => '<processor_id>',
                            'code' => 6,
                            'message' => 'Oops, something went wrong.',
                        ],
                    ],
                ],
            ],
            'Unauthorised' => [
                'description' => 'Unauthorised.',
                'schema' => [
                    '$ref' => '#/definitions/GeneralError',
                ],
                'examples' => [
                    'application/json' => [
                        'error' => [
                            'id' => '<processor_id>',
                            'code' => 4,
                            'message' => 'Invalid token.',
                        ],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden.',
                'schema' => [
                    '$ref' => '#/definitions/GeneralError',
                ],
                'examples' => [
                    'application/json' => [
                        'error' => [
                            'id' => '<processor_id>',
                            'code' => 4,
                            'message' => 'Permission denied.',
                        ],
                    ],
                ],
            ],
        ];

        return json_decode(json_encode($responses, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Get the default definitions object.
     *
     * @return stdClass
     */
    protected function getDefaultDefinitions(): stdClass
    {
        $definitions = [
            'GeneralError' => [
                'type' => 'object',
                'properties' => [
                    'error' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'string',
                            ],
                            'code' => [
                                'type' => 'integer',
                                'format' => 'int64',
                            ],
                            'message' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ]
            ],
        ];

        return json_decode(json_encode($definitions, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default externalDocs element.
     *
     * @return stdClass
     */
    protected function getDefaultExternalDocs(): stdClass
    {
        $externalDocs = [
            'description' => 'Find out more about ApiOpenStudio',
            'url' => 'https://www.apiopenstudio.com',
        ];

        return json_decode(json_encode($externalDocs, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(string $accountName, string $applicationName)
    {
        $definition = [
            'swagger' => self::VERSION,
            'info' => $this->getDefaultInfo($applicationName),
            'host' => $this->settings->__get(['api', 'url']),
            'basePath' => "/$accountName/$applicationName",
            'schemes' => $this->settings->__get(['api', 'protocols']),
            'paths' => [],
            'definitions' => $this->getDefaultDefinitions(),
            'responses' => $this->getDefaultResponses(),
            'externalDocs' => $this->getDefaultExternalDocs(),
        ];

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    public function getAccount(): string
    {
        $matches = explode('/', trim($this->definition->basePath, '/'));
        if (sizeof($matches) != 2) {
            throw new ApiException('invalid basePath in the existing openApi schema');
        }
        return $matches[0];
    }

    /**
     * {@inheritDoc}
     */
    public function getApplication(): string
    {
        $matches = explode('/', trim($this->definition->basePath, '/'));
        if (sizeof($matches) != 2) {
            throw new ApiException('invalid basePath in the existing openApi schema');
        }
        return $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    public function setAccount(string $accountName)
    {
        $matches = explode('/', trim($this->definition->basePath, '/'));
        if (sizeof($matches) != 2) {
            throw new ApiException('invalid basePath in the existing openApi schema');
        }
        $this->definition->basePath = "/$accountName/" . $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $matches = explode('/', trim($this->definition->basePath, '/'));
        if (sizeof($matches) != 2) {
            throw new ApiException('invalid basePath in the existing openApi schema');
        }
        $this->definition->info->title = $applicationName;
        $this->definition->info->description = str_replace(
             " {$matches[1]} ",
            " $applicationName ",
            $this->definition->info->description
        );
        $this->definition->basePath = '/' . $matches[0] . "/$applicationName";
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain()
    {
        $this->definition->host = $this->settings->__get(['api', 'url']);
    }
}
