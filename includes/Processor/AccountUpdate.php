<?php

/**
 * Class AccountUpdate.
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
 * Class AccountUpdate
 *
 * Processor class to update an account.
 */
class AccountUpdate extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Account update',
        'machineName' => 'account_update',
        'description' => 'Rename an account.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The account ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The new name for the account.',
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
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $name = $this->val('name', true);
        if (preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new Core\ApiException(
                "Invalid account name: $name. Only underscore, hyphen or alhpanumeric characters permitted.",
                6,
                $this->id,
                400
            );
        }
        $accid = $this->val('accid', true);

        $accountMapper = new Db\AccountMapper($this->db);

        $account = $accountMapper->findByName($name);
        if (!empty($account->getAccid())) {
            throw new Core\ApiException("Account already exists: $name", 6, $this->id, 400);
        }
        $account = $accountMapper->findByAccid($accid);
        if (empty($account->getAccid())) {
            throw new Core\ApiException("Account does not exist: $accid", 6, $this->id, 400);
        }

        $account->setName($name);
        return $accountMapper->save($account);
    }
}
