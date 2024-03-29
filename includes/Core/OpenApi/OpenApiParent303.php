<?php

/**
 * Class OpenApiParent303.
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

/**
 * Class to generate default elements for OpenApi v3.0.3.
 */
class OpenApiParent303 extends OpenApiParent302
{
    /**
     * OpenApi doc version.
     */
    protected string $version = "3.0.3";
}
