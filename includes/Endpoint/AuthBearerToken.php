<?php

/**
 * Provide Bearer Token header authentication
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

class AuthBearerToken extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Bearer token)',
        'machineName' => 'auth_bearer_token',
        'description' => 'Authentication for remote server, presenting a bearer token in the header.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'token' => [
                'description' => 'The token string (e.g. 907c762e069589c2cd2a229cdae7b8778caa9f07).',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

  /**
   * {@inheritDoc}
   */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Auth (bearer token)', 4);

        $token = $this->val('token', true);

        return array(CURLOPT_HTTPHEADER => "Authorization: Bearer $token");
    }
}
