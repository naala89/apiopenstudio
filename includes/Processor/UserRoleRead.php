<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * User_role table Read.
 */

class UserRoleRead extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User Role read',
    'machineName' => 'user_role_read',
    'description' => 'Fetch a single or all user roles.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'uid' => [
        'description' => 'The user id of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'accid' => [
        'description' => 'The account ID of user roles.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'appid' => [
        'description' => 'The application ID of user roles.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'rid' => [
        'description' => 'The user role ID of user roles.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'order_by' => [
        'description' => 'The column to order the results by.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['uid', 'accid', 'appid', 'rid'],
        'default' => ''
      ],
      'direction' => [
        'description' => 'The direction to order the results.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['asc', 'desc'],
        'default' => 'asc'
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $uid = $this->val('uid', TRUE);
    $accid = $this->val('accid', TRUE);
    $appid = $this->val('appid', TRUE);
    $rid = $this->val('rid', TRUE);
    $order_by = $this->val('order_by', TRUE);
    $direction = $this->val('direction', TRUE);

    $params = [];
    if (!empty($uid)) {
      $params['col']['uid'] = $uid;
    }
    if (!empty($accid)) {
      $params['col']['accid'] = $accid;
    }
    if (!empty($appid)) {
      $params['col']['appid'] = $appid;
    }
    if (!empty($rid)) {
      $params['col']['rid'] = $rid;
    }
    if (!empty($order_by)) {
      $params['order_by'] = $order_by;
    }
    if (!empty($order_by)) {
      $params['direction'] = $direction;
    }

    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $userRoles = empty($params) ? $userRoleMapper->findAll() : $userRoleMapper->findByFilter($params);

    $result = [];
    foreach ($userRoles as $userRole) {
      $result[] = $userRole->dump();
    }

    return $result;
  }
}