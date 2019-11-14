<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * User role create.
 */

class UserRoleCreate extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User Role create',
    'machineName' => 'user_role_create',
    'description' => 'Create a role for a user.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'uid' => [
        'description' => 'The user id of the user.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'accid' => [
        'description' => 'The account ID of user roles.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => ''
      ],
      'appid' => [
        'description' => 'The application ID of user roles.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => ''
      ],
      'rid' => [
        'description' => 'The user role ID of user roles.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $uid = $this->val('uid', TRUE);
    $accid = $this->val('accid', TRUE);
    $accid = !empty($accid) ? $accid : NULL;
    $appid = $this->val('appid', TRUE);
    $appid = !empty($appid) ? $appid : NULL;
    $rid = $this->val('rid', TRUE);

    if ($rid > 2 && empty($appid)) {
      throw new Core\ApiException('Only Administrator or Account manager roles can have NULL assigned to application.', 6, $this->id, 400);
    }
    elseif ($rid > 1 && empty($accid)) {
      throw new Core\ApiException('Only Administrator role can have NULL assigned to account.', 6, $this->id, 400);
    }

    $userRoleMapper = new Db\UserRoleMapper($this->db);

    $userRole = $userRoleMapper->findByFilter(['col' => [
      'uid' => $uid,
      'accid' => $accid,
      'appid' => $appid,
      'rid' => $rid,
    ]]);
    if (!empty($userRole)) {
      throw new Core\ApiException('User role already exists.', 6, $this->id, 400);
    }

    $userRole = new Db\UserRole(NULL, $accid, $appid, $uid, $rid);
    return $userRoleMapper->save($userRole);
  }

}
