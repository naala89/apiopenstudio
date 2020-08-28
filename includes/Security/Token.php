<?php

/**
 * Provide token authentication based on token.
 */

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Db;

class Token extends Core\ProcessorEntity
{
    protected $role = false;
    /**
     * {@inheritDoc}
     */

    /**
     * @var Db\UserMapper
     */
    protected $userMapper;

    protected $details = [
        'name' => 'Token',
        'machineName' => 'token',
        'description' => 'Validate that the user has a valid token.',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'The consumers token.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
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
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Security: ' . $this->details()['machineName']);

        $token = $this->val('token', true);

        // no token
        if (empty($token)) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // invalid token or user not active
        $user = $this->userMapper->findBytoken($token);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        return true;
    }
}
