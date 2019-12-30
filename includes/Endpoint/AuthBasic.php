<?php

/**
 * Provide Basic username/Password authentication
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

class AuthBasic extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Basic User/Pass)',
        'machineName' => 'auth_basic',
        'description' => 'Basic authentication for remote server, using username/password.',
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
        Core\Debug::variable($this->meta, 'Auth Basic', 4);

        $username = $this->val('username', true);
        $password = $this->val('password', true);

        return array(CURLOPT_USERPWD => "$username:$password");
    }
}
