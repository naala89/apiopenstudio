<?php

/**
 * Class OpenApiParent2.
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
 * Class to generate default parent elements for OpenApi v2.0.
 */
class OpenApiParent2 extends OpenApiParentAbstract
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
     * @return array
     *
     * @throws ApiException
     */
    protected function getDefaultInfo(string $applicationName): array
    {
        return [
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
    }

    /**
     * Returns the default responses element.
     *
     * @return array
     */
    protected function getDefaultResponses(): array
    {
        return [
            'description' => 'General Error',
            'schema' => [
                '$ref' => '#/definitions/GeneralError',
                'examples' => [
                    'application/json' => [
                        'error' => [
                            'id' => 'processor_id',
                            'code' => 6,
                            'message' => 'Error details.',
                        ],
                    ]
                ]
            ],
        ];
    }

    protected function getDefaultDefinitions(): array
    {
        return [
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
    }

    /**
     * Returns the default externalDocs element.
     *
     * @return array
     */
    protected function getDefaultExternalDocs(): array
    {
        return [
            'description' => 'Find out more about ApiOpenStudio',
            'url' => 'https://www.apiopenstudio.com',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(string $accountName, string $applicationName)
    {
        $this->definition = [
            'swagger' => self::VERSION,
            'info' => $this->getDefaultInfo($applicationName),
            'host' => $this->settings->__get(['api', 'url']),
            'basePath' => "$accountName/$applicationName",
            'schemes' => $this->settings->__get(['api', 'protocols']),
            'paths' => [],
            'definitions' => $this->getDefaultDefinitions(),
            'responses' => $this->getDefaultResponses(),
            'externalDocs' => $this->getDefaultExternalDocs(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setAccount(string $accountName)
    {
        $this->definition['basePath'] = preg_replace('/.*\//', $accountName, $this->definition['basePath']);
    }

    /**
     * {@inheritDoc}
     */
    public function setApplication(string $applicationName)
    {
        $oldApplicationName = $this->definition['info']['title'];
        $this->definition['info']['title'] = $applicationName;
        $this->definition['info']['description'] = str_replace(
            $oldApplicationName,
            $applicationName,
            $this->definition['info']['description']
        );
        $this->definition['basePath'] = preg_replace('/\/.*/', $applicationName, $this->definition['basePath']);
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain()
    {
        $this->definition['host'] = $this->settings->__get(['api', 'url']);
    }
}
