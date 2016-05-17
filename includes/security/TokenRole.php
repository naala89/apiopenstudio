<?php

/**
 * Provide token authentication based on token in DB and the user's role
 *
 * Meta:
 *    {
 *      "type": "token",
 *      "meta": {
 *        "id":<integer>,
 *        "token": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Security;
use Datagator\Core;
use Datagator\Db;
use Datagator\Processor;

class TokenRole extends Token {

  protected $details = array(
    'name' => 'Token (Role)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a declared role.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor')
      ),
      'role' => array(
        'description' => 'The consumers role.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'string')
      )
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Security\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenRole', 4);

    // no token
    $token = $this->val($this->meta->token);
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // invalid token or user not active
    $db = $this->getDb();
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    $uid = $user->getUid();
    if (empty($uid) || $user->getActive() == 0) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // get rid
    $rid = $this->val($this->meta->role);
    if (!filter_var($rid, FILTER_VALIDATE_INT)) {
      // convert role name to rid
      $roleMapper = new RoleMapper($db);
      $row = $roleMapper->findByName($rid);
      $rid = $row->getRid();
    }

    $userRoleMapper = new Db\UserRoleMapper($db);
    $userRole = $userRoleMapper->findByUserAppRole($uid, $this->request->appId, $rid);
    if (empty($userRole->getId())) {
      throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }
    return true;
  }
}
