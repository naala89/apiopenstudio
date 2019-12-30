<?php

/**
 * Provide Digest username/ Password authentication
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

class AuthDigest extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Digest User/Pass)',
        'machineName' => 'auth_digest',
        'description' => 'Digest authentication for remote server, using username/password.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'username' => [
                'description' => 'The username.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The password.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
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
        Core\Debug::variable($this->meta, 'Auth Digest', 4);

        $username = $this->val('username', true);
        $password = $this->val('password', true);

        return array(
        CURLOPT_USERPWD => "$username:$password",
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST
        );
    }
}
