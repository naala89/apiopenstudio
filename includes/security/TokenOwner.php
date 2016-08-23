<?php

/**
 * Provide token authentication based on token in DB with Owner access
 * This is the only case where the App ID in the URL is now replaced by Acc ID
 */

namespace Datagator\Security;
use Datagator\Core;
use Datagator\Processor;
use Datagator\Db;

class TokenOwner extends Core\ProcessorEntity
{
  protected $role = 'owner';
  protected $details = array(
    'name' => 'Token (Admin)',
    'machineName' => 'tokenOwner',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of owner. In order to validate correctly, the usual appId in the URL is replaced with accId',
    'menu' => 'Security',
    'application' => 'Common',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      )
    ),
  );

  public function process() {
    Core\Debug::variable($this->meta, 'Security TokenOwner', 4);

    // no token
    $token = $this->val('token');
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // invalid token or user not active
    $db = $this->getDb();
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    if (empty($uid = $user->getUid()) || $user->getActive() == 0) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // validate uid and accId in account table
    $accountMapper = new Db\AccountMapper($db);
    $accId = $this->request->appId;
    $account = $accountMapper->findByAccIdUid($accId, $uid);
    if (empty($account->getName())) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }
    return true;
  }
}
