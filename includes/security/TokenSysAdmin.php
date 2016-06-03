<?php

/**
 * Provide token authentication based on token in DB
 */

namespace Datagator\Security;
use Datagator\Core;
use Datagator\Processor;
use Datagator\Db;

class TokenSysAdmin extends Processor\ProcessorBase {

  protected $role = 'SysAdmin';
  protected $details = array(
    'name' => 'Token (SysAdmin)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of sys-admin for application referenced by the appId in the URI.',
    'menu' => 'Security',
    'client' => 'System',
    'application' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'accepts' => array('function')
      )
    ),
  );

  public function process() {
    Core\Debug::variable($this->meta, 'Security TokenSysAdmin', 4);

    // no token
    $token = $this->val($this->meta->token);
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // invalid token or user not active
    $db = $this->getDb();
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    if (empty($user->getUid()) || $user->getActive() == 0) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // get role from DB
    $roleMapper = new Db\RoleMapper($db);
    $this->role = $roleMapper->findByName($this->role);

    // return list of roles for user for this request app
    $userRoleMapper = new Db\UserRoleMapper($db);
    $roles = $userRoleMapper->findByMixed($user->getUid(), null, $this->role->getRid());
    if (sizeof($roles) > 0) {
      return true;
    }

    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
