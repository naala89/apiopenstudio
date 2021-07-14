<?php

/**
 * Class BearerToken.
 *
 * @package    ApiOpenStudio
 * @subpackage Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core;

/**
 * Class BearerToken.
 *
 * Security class to return a bearer token from the current call.
 */
class BearerToken extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Bearer Token',
        'machineName' => 'bearer_token',
        // phpcs:ignore
        'description' => 'Fetch a bearer token from the request header. This takes the form of "Authorization: Bearer <token>"',
        'menu' => 'Security',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        return new Core\DataContainer(Core\Utilities::getAuthHeaderToken(), 'text');
    }
}
