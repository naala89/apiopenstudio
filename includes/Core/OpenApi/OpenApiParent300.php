<?php

/**
 * Class OpenApiParent300.
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
use stdClass;

/**
 * Class to generate default elements for OpenApi v3.0.0.
 */
class OpenApiParent300 extends OpenApiParentAbstract
{
    /**
     * OpenApi doc version.
     */
    protected string $version = "3.0.0";

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
                'name' => 'ApiOpenStudio Public License based on Mozilla Public License 2.0',
                'url' => 'https://www.apiopenstudio.com/license/',
            ],
            'version' => '1.0.0',
        ];

        return json_decode(json_encode($info, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default servers, based on settings.php.
     *
     * @param string $accountName
     * @param string $applicationName
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getDefaultServers(string $accountName, string $applicationName): array
    {
        $servers = [];
        $domain = $this->settings->__get(['api', 'url']);
        foreach ($this->settings->__get(['api', 'protocols']) as $protocol) {
            $newServer = new stdClass();
            $newServer->url = "$protocol://$domain/$accountName/$applicationName";
            $servers[] = $newServer;
        }
        return $servers;
    }

    /**
     * Returns the default components element.
     *
     * @return stdClass
     *
     * @throws ApiException
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
     *
     * @throws ApiException
     */
    protected function getDefaultSchemas(): stdClass
    {
        $dataBlock = [
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
        ];
        if ($this->settings->__get(['api', 'wrap_json_in_response_object'])) {
            $schemas = [
                'GeneralError' => [
                    'type' => 'object',
                    'properties' => [
                        'result' => [
                            'type' => 'string',
                        ],
                        'data' => $dataBlock,
                    ],
                ],
            ];
        } else {
            $schemas = [
                'GeneralError' => [
                    'type' => 'object',
                    'properties' => [
                        'error' => $dataBlock,
                    ],
                ],
            ];
        }

        return json_decode(json_encode($schemas, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Returns the default responses element.
     *
     * @return stdClass
     *
     * @throws ApiException
     */
    protected function getDefaultResponses(): stdClass
    {
        $responses = [];
        $wrap_json_in_response_object = $this->settings->__get(['api', 'wrap_json_in_response_object']);
        $responses['GeneralError'] = [
            'description' => 'General Error',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError',
                    ],
                ],
            ],
        ];
        $responses['Unauthorised'] = [
            'description' => 'Unauthorised',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError',
                    ],
                ],
            ],
        ];
        $responses['Forbidden'] = [
            'description' => 'Forbidden',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError',
                    ],
                ],
            ],
        ];
        if ($wrap_json_in_response_object) {
            $responses['GeneralError']['content']['application/json']['example'] = [
                'result' => 'error',
                'data' => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Oops, something went wrong.',
                ],
            ];
            $responses['Unauthorised']['content']['application/json']['example'] = [
                'result' => 'error',
                'data' => [
                    'id' => '<my_processor_id>',
                    'code' => 4,
                    'message' => 'Invalid token.',
                ],
            ];
            $responses['Forbidden']['content']['application/json']['example'] = [
                'result' => 'error',
                'data' => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Permission denied.',
                ],
            ];
        } else {
            $responses['GeneralError']['content']['application/json']['example'] = [
                'error' => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Oops, something went wrong.',
                ]
            ];
            $responses['Unauthorised']['content']['application/json']['example'] = [
                'error' => [
                    'id' => '<my_processor_id>',
                    'code' => 4,
                    'message' => 'Invalid token.',
                ]
            ];
            $responses['Forbidden']['content']['application/json']['example'] = [
                'error' => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Permission denied.',
                ]
            ];
        }

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
            'openapi' => $this->version,
            'info' => $this->getDefaultInfo($applicationName),
            'servers' => $this->getDefaultServers($accountName, $applicationName),
            'paths' => [],
            'components' => $this->getDefaultComponents(),
            'security' => [],
            'externalDocs' => [
                'description' => 'Find out more about ApiOpenStudio',
                'url' => 'https://www.apiopenstudio.com',
            ],
        ];

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
            $message = 'invalid servers in the openApi schema (' . $server . '). ';
            $message .= 'Could not extract URL for finding account';
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = 'invalid servers in the openApi schema (' . $server . '). ';
            $message .= 'Could not extract URI for finding account';
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
            $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
            $message .= ' Could not extract URL for finding application';
            throw new ApiException($message);
        }
        $matches = explode('/', $urlParts[1]);
        if (sizeof($matches) != 3) {
            $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
            $message .= ' Could not extract URI for finding application';
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
        foreach ($servers as $key => $server) {
            $urlParts = explode('://', $server->url);
            if (sizeof($urlParts) != 2) {
                $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
                $message .= ' Could not extract URL for setting account';
                throw new ApiException($message);
            }
            $protocol = $urlParts[0];
            $matches = explode('/', $urlParts[1]);
            if (sizeof($matches) != 3) {
                $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
                $message .= 'Could not extract URI for setting account';
                throw new ApiException($message);
            }
            $domain = $matches[0];
            $applicationName = $matches[2];
            $server->url = "$protocol://$domain/$accountName/$applicationName";
            $this->definition->servers[$key] = $server;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $servers = $this->definition->servers;
        foreach ($servers as $key => $server) {
            $urlParts = explode('://', $server->url);
            if (sizeof($urlParts) != 2) {
                $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
                $message .= 'Could not extract URL for setting application';
                throw new ApiException($message);
            }
            $protocol = $urlParts[0];
            $matches = explode('/', $urlParts[1]);
            if (sizeof($matches) != 3) {
                $message = 'invalid servers in the openApi schema (' . $server->url . '). ';
                $message .= 'Could not extract URI for setting application';
                throw new ApiException($message);
            }
            $domain = $matches[0];
            $accountName = $matches[1];
            $server->url = "$protocol://$domain/$accountName/$applicationName";
            $this->definition->servers[$key] = $server;
        }

        $this->definition->info->title = $applicationName;
        $description = "These are the resources that belong to the $applicationName application.";
        $this->definition->info->description = $description;
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
