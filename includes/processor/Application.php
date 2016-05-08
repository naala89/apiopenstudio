<?php

/**
 * Account table CRUD.
 */

namespace Datagator\Processor;
use Codeception\Util\Debug;
use Datagator\Core;
use Datagator\Db;

class Application extends ProcessorBase
{
  protected $details = array(
    'name' => 'Application',
    'description' => 'CRUD operations for Application table.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'name' => array(
        'description' => 'The name of the application.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'account' => array(
        'description' => 'The name of the account.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'token' => array(
        'description' => "The users's token.",
        'cardinality' => array(1, 1),
        'accepts' => array('processor'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Account', 4);

    $appName = $this->val($this->meta->applicationName);
    $accName = $this->val($this->meta->accountName);
    $token = $this->val($this->meta->token);
    $action = $this->request->method;
    $db = $this->getDb();

    // need uid to fetch correct account by name
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    $uid = $user->getUid();
    if (empty($uid)) {
      throw new Core\ApiException('No such user, invalid token', 1, $this->id);
    }

    $accountMapper = new Db\AccountMapper($db);
    $account = $accountMapper->findByUidName($uid, $accName);
    $accId = $account->getAccId();
    if (empty($accId)) {
      throw new Core\ApiException('No such account belongs to this user', 1, $this->id);
    }

    switch ($action) {
      case 'post':
        return $this->_create($db, $accId, $appName);
        break;
      case 'get':
        return $this->_fetch($db, $accId, $appName);
        break;
      case 'delete':
        return $this->_delete($db, $accId, $appName);
        break;
      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }

  /**
   * Create an application row.
   *
   * @param $db
   * @param $accId
   * @param $appName
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _create($db, $accId, $appName)
  {
    $applicationMapper = new Db\ApplicationMapper($db);
    $application = $applicationMapper->findByAccIdName($accId, $appName);
    $application->setAccId($accId);
    $application->setName($appName);
    return $applicationMapper->save($application);
  }

  /**
   * Fetch and account.
   *
   * @param $db
   * @param $accId
   * @param $appName
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  private function _fetch($db, $accId, $appName)
  {
    $applicationMapper = new Db\ApplicationMapper($db);
    $application = $applicationMapper->findByAccIdName($accId, $appName);
    return $application->debug();
  }

  /**
   * Delete an account.
   * 
   * @param $db
   * @param $accId
   * @param $appName
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _delete($db, $accId, $appName)
  {
    $accountMapper = new Db\ApplicationMapper($db);
    return $accountMapper->deleteByAccIdName($accId, $appName);
  }
}
