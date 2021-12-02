<?php

/**
 * Class OpenApiPath2.
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

/**
 * Class to generate default path elements for OpenApi v2.0.
 */
class OpenApiPath2 extends OpenApiPathAbstract
{
    /**
     * {@inheritDoc}
     */
    public function setDefault(Resource $resource)
    {
        $this->definition = [
            $resource->getUri() => [
                $resource->getMethod() => [
                    'description' => $resource->getDescription(),
                    'summary' => $resource->getName(),
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
                    ],
                    'default' => [
                        'description' => 'General error',
                        'schema' => [
                            'type' => 'object',
                            '$ref' => '#/definitions/GeneralError',
                        ],
                    ],
                ],
            ],
        ];
    }
}
