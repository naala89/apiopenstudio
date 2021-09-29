<?php

/**
 * Class InviteAccept.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class InviteAccept
 *
 * Processor class accept a user invite with an invite token.
 */
class InviteAccept extends Core\ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    private Db\UserMapper $userMapper;

    /**
     * Invite mapper class.
     *
     * @var Db\InviteMapper
     */
    private Db\InviteMapper $inviteMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Accept an invite',
        'machineName' => 'invite_accept',
        'description' => 'Accept an invite to ApiOpenStudio.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The invite token.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * InviteAccept constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db, $logger);
        $this->inviteMapper = new Db\InviteMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $token = $this->val('token', true);

        $invite = $this->inviteMapper->findByToken($token);
        if (empty($email = $invite->getEmail())) {
            throw new Core\ApiException("Invalid invite token", 6, $this->id, 400);
        }

        $user = new Db\User(null, 1, $email, null, null, null, $email);
        $this->userMapper->save($user);
        $this->inviteMapper->delete($invite);

        return new Core\DataContainer('true', 'text');
    }
}
