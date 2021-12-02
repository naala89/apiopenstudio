<?php

/**
 * Class OpenApiPath3.
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
 * Class to generate default elements for OpenApi v3.0.
 */
class OpenApiPath3 extends OpenApiPathAbstract
{
    public function setDefault(Resource $resource): array
    {
        return [
            $resource->getUri() => [
                $resource->getMethod() => [
                    'description' => $resource->getDescription(),
                    'summary' => $resource->getName(),
                ],
            ],
        ];
    }
}
