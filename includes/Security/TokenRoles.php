<?php

namespace Gaterdata\Security;
use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * Provide token authentication based on token in DB and the user's roles.
 */

class TokenRoles extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Token (Roles)',
    'machineName' => 'token_roles',
    'description' => 'Validate that the user has a valid token and roles.',
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
      'roles' => [
        'description' => 'A collection of user_role.',
        'cardinality' => [1, '*'],
        'literalAllowed' => FALSE,
        'limitFunctions' => ['collection'],
        'limitTypes' => [],
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
    $roleNames = $this->val('roles', TRUE);
    
    // If a role that fits is found return TRUE, otherwise fall through to the exception.
    foreach($roleNames as $roleName) {
      Debug::variable(($roleName), 'validating user rolename');
      $row = $roleMapper->findByName($roleName);
      if (empty($rid = $row->getRid())) {
        throw new Core\ApiException('Invalid role defined', 4, $this->id, 401);
      }
      $rid = $row->getRid();
      $userRoles = $userRoleMapper->findByRidUid($rid, $uid);
      if (!empty($userRoles)) {
        return TRUE;
      }
    }
    
    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
