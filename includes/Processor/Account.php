<?php

/**
 * Account table CRUD.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class Account extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Account',
    'machineName' => 'account',
    'description' => 'CRUD operations for accounts.',
    'menu' => 'Admin',
    'application' => 'Administrator',
    'input' => [
      'accountName' => [
        'description' => 'The name of the account.',
        'cardinality' => [1, 1],
        'literalAllowed' => FALSE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'oldName' => [
        'description' => 'The old name of the account. This is only used if updating the name.',
        'cardinality' => [0, 1],
        'literalAllowed' => FALSE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $accountName = $this->val('accountName', TRUE);
    $oldName = $this->val('oldName', TRUE);
    $method = $this->request->getMethod();

    $accountMapper = new Db\AccountMapper($this->db);

    switch ($method) {

      case 'post':
        $account = $accountMapper->findByName(!empty($oldName) ? $oldName : $accountName);
        $account->setName($accountName);
        return $accountMapper->save($account);
        break;

      case 'get':
        $account = $accountMapper->findByName($accountName);
        return $account->dump();
        break;

      case 'delete':
        $account = $accountMapper->findByName($accountName);
        return $accountMapper->delete($account);
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }
}
