<?php
/**
 * Class InviteAccept.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;
use Monolog\Logger;

/**
 * Class InviteAccept
 *
 * Processor class accept a user invite with an invite token.
 */
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
     * @var array Details of the processor.
     *
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
     * InviteAccept constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
        $this->inviteMapper = new Db\InviteMapper($db);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

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
