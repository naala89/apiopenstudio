<?php

/**
 * User read.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class UserRead extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User read',
    'machineName' => 'user_read',
    'description' => 'Fetch a single or multiple users.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'uid' => [
        'description' => 'The user ID of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'username' => [
        'description' => 'The username of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'email' => [
        'description' => 'The email of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'keyword' => [
        'description' => 'User keyword to filter by, this is applied to username, first name, last name and email.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string', 'integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'orderBy' => [
        'description' => 'Order by column.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['uid', 'username', 'name_first', 'name_last', 'email'],
        'default' => ''
      ],
      'direction' => [
        'description' => 'Order by direction.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['asc', 'desc'],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $uid = $this->val('uid', TRUE);
    $username = $this->val('username', TRUE);
    $email = $this->val('email', TRUE);
    $keyword = $this->val('keyword', TRUE);
    $orderBy = $this->val('orderBy', TRUE);
    $orderBy = empty($orderBy) ? 'uid' : $orderBy;
    $direction = $this->val('direction', TRUE);
    $direction = empty($direction) ? 'asc' : $direction;

    $userMapper = new Db\UserMapper($this->db);
    $result = [];

    if (!empty($uid)) {
      // Find by UID.
      $user = $userMapper->findByUid($uid);
      if (empty($user->getUid())) {
        throw new Core\ApiException("User does not exist, uid: $uid", 6, $this->id, 400);
      }
      $result = $user->dump();
    }
    elseif (!empty($username)) {
      // Find by username.
      $user = $userMapper->findByUsername($username);
      if (empty($user->getUid())) {
        throw new Core\ApiException("User does not exist, username: $username", 6, $this->id, 400);
      }
      $result = $user->dump();
    }
    elseif (!empty($email)) {
      // Find by email.
      $user = $userMapper->findByEmail($email);
      if (empty($user->getUid())) {
        throw new Core\ApiException("User does not exist, email: $email", 6, $this->id, 400);
      }
      $result = $user->dump();
    }
    elseif (!empty($keyword)) {
      // Find by keyword.
      $params = [
        'filter' => [
          ['keyword' => "%$keyword%", 'column' => 'username'],
          ['keyword' => "%$keyword%", 'column' => 'name_first'],
          ['keyword' => "%$keyword%", 'column' => 'name_last'],
          ['keyword' => "%$keyword%", 'column' => 'email'],
        ],
        'order_by' => $orderBy,
        'direction' => $direction,
      ];
      $users = $userMapper->findAll($params);
      foreach ($users as $user) {
        $result[] = $user->dump();
      }
    }
    else {
      // Fetch all.
      $params = [
        'order_by' => $orderBy,
        'direction' => $direction,
      ];
      $users = $userMapper->findAll($params);
      foreach ($users as $user) {
        $result[] = $user->dump();
      }
    }

    return $result;

  }
}
