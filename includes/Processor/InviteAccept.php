<?php

/**
 * Class InviteAccept.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\InviteMapper;
use ApiOpenStudio\Db\User;
use ApiOpenStudio\Db\UserMapper;

/**
 * Class InviteAccept
 *
 * Processor class accept a user invite with an invite token.
 */
class InviteAccept extends ProcessorEntity
{
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
                'default' => null,
            ],
        ],
    ];

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * Invite mapper class.
     *
     * @var InviteMapper
     */
    private InviteMapper $inviteMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db, $logger);
        $this->inviteMapper = new InviteMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();
        $token = $this->val('token', true);

        try {
            $invite = $this->inviteMapper->findByToken($token);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($email = $invite->getEmail())) {
            throw new ApiException("Invalid invite token", 6, $this->id, 400);
        }

        $user = new User(null, 1, $email, null, null, null, $email);
        try {
            $this->userMapper->save($user);
            $this->inviteMapper->delete($invite);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer('true', 'text');
    }
}
