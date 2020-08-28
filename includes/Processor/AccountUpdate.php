<?php

/**
 * Account update.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class AccountUpdate extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
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
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The new name for the account.',
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
