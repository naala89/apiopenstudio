<?php

/**
 * Class AuthBearerToken.
 *
 * @package    ApiOpenStudio\Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Endpoint;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;

/**
 * Class AuthBearerToken
 *
 * Provide Auth Bearer authentication to a resource.
 */
class AuthBearerToken extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Auth (Bearer token)',
        'machineName' => 'auth_bearer_token',
        'description' => 'Authentication for remote server, presenting a bearer token in the header.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'token' => [
                'description' => 'The token string (e.g. 907c762e069589c2cd2a229cdae7b8778caa9f07).',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        parent::process();

        $token = $this->val('token', true);

        return new DataContainer([CURLOPT_HTTPHEADER => "Authorization: Bearer $token"], 'array');
    }
}
