<?php

/**
 * Trait HandleExceptionTrait.
 *
 * @package    ApiOpenStudio\Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Cli;

use ApiOpenStudio\Core\ApiException;

/**
 * Trait HandleExceptionTrait.
 *
 * Trait to handle ApiExceptions in a generic way for CLI.
 */
trait HandleExceptionTrait
{
    /**
     * Handle exceptions for CLI in a generic way.
     *
     * @param ApiException $e
     *
     * @return void
     */
    protected function handleException(ApiException $e)
    {
        echo "An error occurred, please check the logs.\n";
        echo $e->getMessage() . "\n";
        exit;
    }
}
