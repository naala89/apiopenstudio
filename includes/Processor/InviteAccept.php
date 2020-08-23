<?php

/**
 * Invite accept - process an invite and create the user.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class InviteAccept extends Core\ProcessorEntity
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var Db\InviteMapper
     */
    private $inviteMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Accept an invite',
        'machineName' => 'invite_accept',
        'description' => 'Accept an invite to GaterData.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The invite token.',
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
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
        $this->inviteMapper = new Db\InviteMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);

        $invite = $this->inviteMapper->findByToken($token);
        if (empty($email = $invite->getEmail())) {
            throw new Core\ApiException("Invalid invite token.", 6, $this->id, 400);
        }

        $user = new Db\User(null, 1, $email, null, null, null, $email);
        $this->userMapper->save($user);
        $this->inviteMapper->delete($invite);

        return new Core\DataContainer('true', 'text');
    }
}
