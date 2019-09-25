<?php

/**
 * Account table CRUD.
 *
 * @TODO: This currently assumes that account name is unique
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class Application extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Application',
    'machineName' => 'datagatorApplication',
    'description' => 'CRUD operations for Datagator applications.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'applicationName' => array(
        'description' => 'The name of the application.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'accountName' => array(
        'description' => 'The name of the account.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor DatagatorApplication', 4);

    $applicationName = $this->val('applicationName');
    $method = $this->request->method;
    $db = $this->getDb();

    $accountId = '';
    if (!empty($this->meta->accountName)) {
      $accountName = $this->val('accountName');
      $accountMapper = new Db\AccountMapper($db);
      $account = $accountMapper->findByName($accountName);
      $accountId = $account->getAccId();
      if (empty($accountId)) {
        throw new Core\ApiException("No such account exists: $accountName", 1, $this->id);
      }
    }

    $applicationMapper = new Db\ApplicationMapper($db);

    switch ($method) {

      case 'post':
        $application = $applicationMapper->findByName($applicationName);
        $application->setAccId($accountId);
        $application->setName($applicationName);
        Core\Debug::variable($application->debug());
        return $applicationMapper->save($application);
        break;

      case 'get':
        if (!empty($accountId)) {
          return $applicationMapper->findByAccId($accountId);
        } elseif (!empty($applicationName)) {
          return $applicationMapper->findByName($applicationName);
        }
        return new Db\Account();
        break;

      case 'delete':
        if (!empty($accountId)) {
          $account = $applicationMapper->findByAccId($accountId);
        } elseif (!empty($applicationName)) {
          $account = $applicationMapper->findByName($applicationName);
        } else {
          $account = new Db\Account();
        }
        return $applicationMapper->delete($account);
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }
}
