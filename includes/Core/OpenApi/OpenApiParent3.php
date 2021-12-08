<?php

/**
 * Class OpenApiParent3.
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

/**
 * Class to generate default elements for OpenApi v3.0.
 */
class OpenApiParent3 extends OpenApiParentAbstract
{
    /**
     * OpenApi doc version.
     */
    protected const VERSION = "3.0.3";

    /**
     * Returns the default info element.
     *
     * @param string $applicationName
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getDefaultInfo(string $applicationName): array
    {
        return [
            'title' => $applicationName,
            'description' => "These are the resources that belong to the $applicationName application.",
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
    }

    /**
     * Returns the default components element.
     *
     * @return array
     */
    protected function getDefaultComponents(): array
    {
        return [
            'schemas' => $this->getDefaultSchemas(),
            'responses' => $this->getDefaultResponses(),
            'securitySchemes' => $this->getDefaultSecuritySchemes(),
        ];
    }

    /**
     * Returns the default schemas element.
     *
     * @return array
     */
    protected function getDefaultSchemas(): array
    {
        return [
            'GeneralError' => [
                'type' => 'object',
                'properties' => [
                    'error' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'code' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'message' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the default responses element.
     *
     * @return array
     */
    protected function getDefaultResponses(): array
    {
        return [
            'GeneralError' => [
                'description' => 'General Error',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Oops, something went wrong.',
                            ]
                        ],
                    ],
                ],
            ],
            'Unauthorised' => [
                'description' => 'Unauthorised',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 4,
                                'message' => 'Invalid token.',
                            ]
                        ],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Permission denied.',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the default securitySchemes element.
     *
     * @return array
     */
    protected function getDefaultSecuritySchemes(): array
    {
        return [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(string $accountName, string $applicationName)
    {
        $this->definition = [
            'openapi' => self::VERSION,
            'info' => $this->getDefaultInfo($applicationName),
            'servers' => [],
            'paths' => [],
            'components' => $this->getDefaultComponents(),
            'security' => [],
            'externalDocs' => [
                'description' => 'Find out more about ApiOpenStudio',
                'url' => 'https://www.apiopenstudio.com',
            ],
        ];
        foreach ($this->settings->__get(['api', 'protocols']) as $protocol) {
            $this->definition['servers'][] = [
                'url' => "$protocol://" . $this->settings->__get(['api', 'url']) . "/$accountName/$applicationName"
            ];
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getAccount(): string
    {
        $servers = $this->definition['servers'];
        $urlParts = explode('://', $servers[0]['url']);
        if (sizeof($urlParts) != 2) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        return $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    public function getApplication(): string
    {
        $servers = $this->definition['servers'];
        $urlParts = explode('://', $servers[0]['url']);
        if (sizeof($urlParts) != 2) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        return $matches[2];
    }

    /**
     * {@inheritDoc}
     */
    public function setAccount(string $accountName)
    {
        $servers = $this->definition['servers'];
        $urlParts = explode('://', $servers[0]['url']);
        if (sizeof($urlParts) != 2) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $this->definition['servers'] = $urlParts[0] . '://' . $matches[0] . "/$accountName/" . $matches[2];
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $servers = $this->definition['servers'];
        $urlParts = explode('://', $servers[0]['url']);
        if (sizeof($urlParts) != 2) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            throw new ApiException('invalid servers in the existing openApi schema');
        }
        $this->definition['servers'] = $urlParts[0] . '://' . $matches[0] . '/' . $matches[1] . "/$applicationName";

        $this->definition['info']['title'] = $applicationName;
        $this->definition['info']['description'] = str_replace(
            ' ' . $matches[1] . ' ',
            " $applicationName ",
            $this->definition['info']['description']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain()
    {
        $this->definition['servers'] = [
            'url' => $this->settings->__get(['api', 'url']),
        ];
    }
}
