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
            'description' => 'The name of the account.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => ['text'],
            'limitValues' => [],
            'default' => ''
          ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $name = $this->val('name', true);

        $accountMapper = new Db\AccountMapper($this->db);

        $account = $accountMapper->findByName($name);
        if (!empty($account->getAccid())) {
            throw new Core\ApiException("Account already exists: $name", 6, $this->id, 400);
        }

        $account->setName($name);
        return $accountMapper->save($account);
    }
}
