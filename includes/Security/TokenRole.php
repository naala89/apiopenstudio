<?php

namespace Gaterdata\Security;
use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * Provide token authentication based on token in DB and the user's role.
 */

class TokenRole extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Token (Role)',
    'machineName' => 'token_role',
    'description' => 'Validate that the user has a valid token and role. This is faster than Token Roles,',
    'menu' => 'Security',
    'application' => 'Common',
    'input' => [
      'token' => [
        'description' => 'The consumers token.',
        'cardinality' => [1, 1],
        'literalAllowed' => FALSE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'role' => [
        'description' => 'A user_role.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
    ],
  ];

  /**
   * @return bool
   * @throws \Gaterdata\Core\ApiException
   * @throws \Gaterdata\Security\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Security TokenRole', 4);

    // no token
    $token = $this->val('token');
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // invalid token or user not active
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findBytoken($token);
    $uid = $user->getUid();
    if (empty($uid) || $user->getActive() == 0) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // Get roles and validate the user.
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $roleMapper = new Db\RoleMapper($this->db);
    $roleName = $this->val('role');
    // If a role that fits is found return TRUE, otherwise fall through to the exception.
    $role = $roleMapper->findByName($roleName);
    if (empty($rid = $role->getRid())) {
      throw new Core\ApiException('Invalid role defined', 4, $this->id, 401);
    }
    $userRoles = $userRoleMapper->findByRidUid($rid, $uid);
    if (!empty($userRoles)) {
      return TRUE;
    }
    
    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
