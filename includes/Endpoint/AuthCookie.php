<?php

/**
 * Provide cookie authentication
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

class AuthCookie extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Cookie)',
        'machineName' => 'auth_cookie',
        'description' => 'Authentication for remote server, using a cookie.',
        'menu' => 'Authentication',
        'input' => [
            'cookie' => [
                'description' => 'The cookie string.',
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
        Core\Debug::variable($this->meta, 'Auth Cookie', 4);

        $cookie = $this->val('cookie', true);

        return array(CURLOPT_COOKIE => $cookie);
    }
}
