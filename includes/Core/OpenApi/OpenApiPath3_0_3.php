<?php

/**
 * Class OpenApiPath3_0_3.
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
 * Class to generate default elements for OpenApi v3.0.3.
 */
class OpenApiPath3_0_3 extends OpenApiPathAbstract
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
                    'summary' => $resource->getName(),
                    'description' => $resource->getDescription(),
                    'tags' => [$path],
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

        $this->definition = json_decode(json_encode($definition, JSON_UNESCAPED_SLASHES));
    }

}
