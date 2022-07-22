<?php

/**
 * Class BearerToken.
 *
 * @package    ApiOpenStudio\Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;

/**
 * Class BearerToken.
 *
 * Security class to return a bearer token from the current call.
 */
class BearerToken extends ProcessorEntity
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
     * @return DataContainer Result of the processor.
     */
    public function process(): DataContainer
    {
        parent::process();

        return new DataContainer(Utilities::getAuthHeaderToken(), 'text');
    }
}
