<?php

/**
 * Account Create.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class AccountCreate extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Account create',
        'machineName' => 'account_create',
        'description' => 'Create an account.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The name of the account. This must contain alphanumeric characters only.',
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

        $accountMapper = new Db\AccountMapper($this->db);

        $account = $accountMapper->findByName($name);
        if (!empty($account->getAccid())) {
            throw new Core\ApiException("Invalid account name: $name. This account already exists.", 6, $this->id, 400);
        }

        $account->setName($name);
        return $accountMapper->save($account);
    }
}
