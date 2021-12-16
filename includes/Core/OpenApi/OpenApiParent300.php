<?php

/**
 * Class OpenApiParent300.
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
 * Class to generate default elements for OpenApi v3.0.0.
 */
class OpenApiParent300 extends OpenApiParentAbstract
{
    /**
     * OpenApi doc version.
     */
    protected const VERSION = "3.0.0";

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

        return json_decode(json_encode($info, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default components element.
     *
     * @return stdClass
     */
    protected function getDefaultComponents(): stdClass
    {
        $components = [
            'schemas' => $this->getDefaultSchemas(),
            'responses' => $this->getDefaultResponses(),
            'securitySchemes' => $this->getDefaultSecuritySchemes(),
        ];

        return json_decode(json_encode($components, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default schemas element.
     *
     * @return stdClass
     */
    protected function getDefaultSchemas(): stdClass
    {
        $schemas = [
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

        return json_decode(json_encode($schemas, JSON_UNESCAPED_SLASHES));
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

        return json_decode(json_encode($responses, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default securitySchemes element.
     *
     * @return stdClass
     */
    protected function getDefaultSecuritySchemes(): stdClass
    {
        $securitySchemes = [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ];

        return json_decode(json_encode($securitySchemes, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(string $accountName, string $applicationName)
    {
        $definition = [
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
            $definition['servers'][] = [
                'url' => "$protocol://" . $this->settings->__get(['api', 'url']) . "/$accountName/$applicationName"
            ];
        }

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }

    /**
     * {@inheritDoc}
     */
    public function getAccount(): string
    {
        $servers = $this->definition->servers;
        $server = $servers[0];
        $urlParts = explode('://', $server->url);
        if (sizeof($urlParts) != 2) {
            $message = "invalid servers in the openApi schema ($server). Could not extract URL for finding account";
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = "invalid servers in the openApi schema ($server). Could not extract URI for finding account";
            throw new ApiException($message);
        }
        return $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    public function getApplication(): string
    {
        $servers = $this->definition->servers;
        $server = $servers[0];
        $urlParts = explode('://', $server->url);
        if (sizeof($urlParts) != 2) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= " Could not extract URL for finding application";
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= "Could not extract URI for finding application";
            throw new ApiException($message);
        }
        return $matches[2];
    }

    /**
     * {@inheritDoc}
     */
    public function setAccount(string $accountName)
    {
        $servers = $this->definition->servers;
        $server = $servers[0];
        $urlParts = explode('://', $server->url);
        if (sizeof($urlParts) != 2) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= " Could not extract URL for setting account.";
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= " Could not extract URI for setting account.";
            throw new ApiException($message);
        }
        $this->definition->servers = [$urlParts[0] . '://' . $matches[0] . "/$accountName/" . $matches[2]];
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $servers = $this->definition->servers;
        $server = $servers[0];
        $urlParts = explode('://', $server->url);
        if (sizeof($urlParts) != 2) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= "Could not extract URL for setting application.";
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = "invalid servers in the openApi schema ($server->url).";
            $message .= " Could not extract URI for setting application.";
            throw new ApiException($message);
        }
        $this->definition->servers = [$urlParts[0] . '://' . $matches[0] . '/' . $matches[1] . "/$applicationName"];

        $this->definition->info->title = $applicationName;
        $this->definition->info->description = str_replace(
            ' ' . $matches[1] . ' ',
            " $applicationName ",
            $this->definition->info->description
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain()
    {
        $servers = [
            'url' => $this->settings->__get(['api', 'url']),
        ];
        $this->definition->servers = json_decode(json_encode($servers, JSON_UNESCAPED_SLASHES));
    }
}
