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
   * @var array
   *  The processor details.
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
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'name' => [
        'description' => 'The new name for the account.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  /**
   *
   * @return array|bool|Core\Error
   * @throws Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $name = $this->val('name', TRUE);
    $accid = $this->val('accid', TRUE);

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