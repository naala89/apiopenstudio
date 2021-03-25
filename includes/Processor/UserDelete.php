<?php

/**
 * Class UserDelete.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class UserDelete
 *
 * Processor class to delete a user.
 */
class UserDelete extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'User delete',
        'machineName' => 'user_delete',
        'description' => 'Delete a user.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'The user ID of the user to delete.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => ''
            ],
        ],
    ];

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

        if (empty($uid = $this->val('uid', true))) {
            throw new Core\ApiException("Cannot process - no uid supplied", 6, $this->id, 400);
        }

        $userMapper = new Db\UserMapper($this->db);

        $user = $userMapper->findByUid($uid);
        if (empty($user->getUid())) {
            throw new Core\ApiException("User does not exist, uid: $uid", 6, $this->id, 400);
        }

        return $userMapper->delete($user);
    }
}
