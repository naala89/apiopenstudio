<?php

/**
 * Account table CRUD.
 *
 * @TODO: This currently assumes that account name is unique
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\Config;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

class Application extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Application',
    'machineName' => 'application',
    'description' => 'CRUD operations for applications.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'accountName' => [
        'description' => 'The name of the account the application is assigned to.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'applicationName' => [
        'description' => 'The name of the application.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'newAccountName' => [
        'description' => 'The new name of the account (only used if updating an application).',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'newApplicationName' => [
        'description' => 'The new name of the application (only used if updating an application).',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
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

    $accountMapper = new Db\AccountMapper($this->db);
    $applicationMapper = new Db\ApplicationMapper($this->db);

    $accountName = $this->val('accountName', TRUE);
    $applicationName = $this->val('applicationName', TRUE);
    $method = $this->request->getMethod();

    $account = $accountMapper->findByName($accountName);
    $accountId = $account->getAccId();
    if (empty($accountId)) {
      throw new Core\ApiException("No such account exists: $accountName", 6, $this->id);
    }

    $application = $applicationMapper->findByAccidAppname($accountId, $applicationName);

    switch ($method) {

      case 'post':
        $newAccountName = $this->val('newAccountName', TRUE);
        $newApplicationName = $this->val('newApplicationName', TRUE);
        if (!empty($newAccountName)) {
          $account = $accountMapper->findByName($newAccountName);
          if (empty($accountId = $account->getAccId())) {
            throw new Core\ApiException("No such new account exists: $newAccountName", 6, $this->id);
          }
        }
        $application->setAccId($accountId);
        $application->setName(!empty($newApplicationName) ? $newApplicationName : $applicationName);
        return $applicationMapper->save($application);
        break;

      case 'get':
        if (empty($application->getAppid())) {
          throw new Core\ApiException("Application $applicationName in account $accountName does not exist", 6, $this->id);
        }
        return $application->dump();
        break;

      case 'delete':
        if (empty($application->getAppid())) {
          throw new Core\ApiException("Application $applicationName in account $accountName does not exist", 6, $this->id);
        }
        return $applicationMapper->delete($application);
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }
}
