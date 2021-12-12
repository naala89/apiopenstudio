<?php

/**
 * Class OpenApiPath2_0.
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
class OpenApiPath2_0 extends OpenApiPathAbstract
{
    /**
     * {@inheritDoc}
     */
    public function setDefault(Resource $resource)
    {
        $path = '/' . $resource->getUri();
        $definition = [
            $path => [
                $resource->getMethod() => [
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

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }
}
