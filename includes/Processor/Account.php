<?php

/**
 * Account CRUD.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;
use Gaterdata\Core\Debug;

class Account extends Core\ProcessorEntity
{
  /**
   * @var array
   *  The processor details.
   */
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
      'keyword' => [
        'description' => 'Keyword to filter by in the account name. This is only used iwhen getting "all" accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'order_by' => [
        'description' => 'Order by column. This is only used when getting "all" accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['accid', 'name'],
        'default' => ''
      ],
      'direction' => [
        'description' => 'Order by direction. This is only used when getting "all" accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['asc', 'desc'],
        'default' => ''
      ],
    ],
  ];

  /**
   * process
   *
   * @return mixed
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $accountName = $this->val('accountName', TRUE);
    $oldName = $this->val('oldName', TRUE);
    $method = $this->request->getMethod();

    switch ($method) {
      case 'post':
        return $this->createUpdate($accountName, $oldName);
        break;

      case 'get':
        return $this->get($accountName);
        break;

      case 'delete':
        return $this->delete($accountName);
        break;

      default:
        throw new Core\ApiException('Invalid action', 3, $this->id);
        break;
    }
  }

  /**
   * Get account.
   * 
   * if $accountName == 'all' then return an array of all accounts.
   *
   * @param string $accountName
   * 
   * @throws ApiException
   *
   * @return array
   */
  private function get($accountName) {
    $accountMapper = new Db\AccountMapper($this->db);

    if ($accountName == 'all') {
      // Only need to add filters if fetching all.
      $keyword = $this->val('keyword', TRUE);
      $orderBy = $this->val('order_by', TRUE);
      $direction = $this->val('direction', TRUE);
      $params = [];
      if (!empty($keyword)) {
        $params['filter'][] = [
          'keyword' => "%$keyword%",
          'column' => "name",
        ];
      }
      if (!empty($orderBy)) {
        $params['order_by'] = $orderBy;
      }
      if (!empty($direction)) {
        $params['direction'] = $direction;
      }

      $rows = $accountMapper->findAll($params);
      $result = [];
      foreach ($rows as $row) {
        $result[$row->getAccid()] = $row->getName();
      }
      return $result;
    }

    $account = $accountMapper->findByName($accountName);
    if (empty($account->getAccid())) {
      throw new Core\ApiException('Account does not exist', 6, $this->id, 400);
    }
    return $account->dump();
  }

  /**
   * Create or Update an acccount
   *
   * @param  mixed $accountName
   * @param  mixed $oldName
   * 
   * @throws ApiException
   *
   * @return boolean
   */
  private function createUpdate($accountName, $oldName) {
    $accountMapper = new Db\AccountMapper($this->db);

    if (empty($oldName)) {
      // This is a create request.
      $account = $accountMapper->findByName($accountName);
      if (!empty($account->getAccid())) {
        throw new Core\ApiException('Account already exists', 6, $this->id, 400);
      }
    } else {
      // This is an update request.
      $account = $accountMapper->findByName($oldName);
      if (empty($account->getAccid())) {
        throw new Core\ApiException('Account does not exist exist', 6, $this->id, 400);
      }
    }

    // Fall through and save.
    $account->setName($accountName);
    return $accountMapper->save($account);
  }

  /**
   * Delete an account.
   *
   * @param  mixed $accountName
   * 
   * @throws ApiException
   *
   * @return boolean
   */
  private function delete($accountName) {
    $accountMapper = new Db\AccountMapper($this->db);

    $account = $accountMapper->findByName($accountName);
    if (empty($account->getAccid())) {
      throw new Core\ApiException('Account does not exist',6, $this->id, 400);
    }

    // Do not delete if applications are attached to the account.
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $applications = $applicationMapper->findByAccid($account->getAccid());
    if (!empty($applications)) {
      throw new Core\ApiException('Cannot delete the account, applications are assigned to the account',6, $this->id, 400);
    }

    return $accountMapper->delete($account);
  }
}
