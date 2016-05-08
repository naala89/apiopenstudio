<?php

/**
 * Account table CRUD.
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

class Account extends ProcessorBase
{
  protected $details = array(
    'name' => 'Account',
    'description' => 'CRUD operations for Account table.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'name' => array(
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

    $accName = $this->val($this->meta->name);
    $token = $this->val($this->meta->token);
    $action = $this->request->method;
    $db = $this->getDb();

    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    $uid = $user->getUid();
    if (empty($uid)) {
      throw new Core\ApiException('No such user, invalid token', 1, $this->id);
    }

    switch ($action) {
      case 'post':
        return $this->_create($db, $uid, $accName);
        break;
      case 'get':
        return $this->_fetch($db, $uid, $accName);
        break;
      case 'delete':
        return $this->_delete($db, $uid, $accName);
        break;
      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }

  /**
   * Create an account row.
   *
   * @param $db
   * @param $uid
   * @param $accName
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _create($db, $uid, $accName)
  {
    $accountMapper = new Db\AccountMapper($db);
    $account = $accountMapper->findByUidName($uid, $accName);
    $account->setUid($uid);
    $account->setName($accName);
    return $accountMapper->save($account);
  }

  /**
   * Fetch and account.
   *
   * @param $db
   * @param $uid
   * @param $accName
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  private function _fetch($db, $uid, $accName)
  {
    $accountMapper = new Db\AccountMapper($db);
    $account = $accountMapper->findByUidName($uid, $accName);
    return $account->debug();
  }

  /**
   * Delete an account.
   *
   * @param $db
   * @param $uid
   * @param $accName
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _delete($db, $uid, $accName)
  {
    $accountMapper = new Db\AccountMapper($db);
    return $accountMapper->deleteByUidName($uid, $accName);
  }
}
