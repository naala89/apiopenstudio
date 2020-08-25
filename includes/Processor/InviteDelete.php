<?php

/**
 * Invite delete.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class InviteDelete extends Core\ProcessorEntity
{
    /**
     * @var Db\InviteMapper
     */
    private $inviteMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Invite delete',
        'machineName' => 'invite_delete',
        'description' => 'Delete an invite by ID.',
        'menu' => 'Admin',
        'input' => [
            'iid' => [
                'description' => 'Invite ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
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
        $this->inviteMapper = new Db\InviteMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $iid = $this->val('iid', true);

        $invite = $this->inviteMapper->findByIid($iid);

        if (empty($invite->getIid())) {
            throw new Core\ApiException('Invalid iid: ' . $iid);
        }

        $result = $this->inviteMapper->delete($invite);
        return new Core\DataContainer('Deleted user invite for ' . $invite->getEmail(), 'text');
    }
}
