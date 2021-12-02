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
            'servers' => [
                'url' => $this->settings->__get(['api', 'url']),
            ],
            'paths' => [],
            'components' => $this->getDefaultComponents(),
            'security' => [
                'bearer_token' => [],
            ],
            'externalDocs' => [
                'description' => 'Find out more about ApiOpenStudio',
                'url' => 'https://www.apiopenstudio.com',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setAccount(string $accountName)
    {
        // Do nothing;
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $oldApplicationName = $this->definition['info']['title'];
        $this->definition['info']['title'] = $applicationName;
        $this->definition['info']['description'] = str_replace($oldApplicationName, $applicationName, $this->definition['info']['description']);
        $this->definition['basePath'] = preg_replace('/\/.*/', $applicationName, $this->definition['basePath']);
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
