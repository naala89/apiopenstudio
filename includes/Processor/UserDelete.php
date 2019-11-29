<?php

/**
 * User delete.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class UserDelete extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'User delete',
    'machineName' => 'user_delete',
    'description' => 'Delete a user.',
    'menu' => 'Admin',
    'input' => [
      'uid' => [
        'description' => 'The user ID of the user.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    if (empty($uid = $this->val('uid', TRUE))) {
      throw new Core\ApiException("Cannot process - no uid supplied", 6, $this->id, 400);
    }

    $userMapper = new Db\UserMapper($this->db);

    // Find by UID.
    $user = $userMapper->findByUid($uid);
    if (empty($user->getUid())) {
      throw new Core\ApiException("User does not exist, uid: $uid", 6, $this->id, 400);
    }

    return $userMapper->delete($user);

  }
}
