<?php

/**
 * Create an invite.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class InviteCreate extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Invite create',
    'machineName' => 'invite_create',
    'description' => 'Create and send an invite.',
    'menu' => 'Admin',
    'input' => [
      'email' => [
        'description' => "The user's email.",
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
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

    $email = $this->val('email', TRUE);

    $inviteMapper = new Db\InviteMapper($this->db);
    $userMapper = new Db\UserMapper($this->db);

    $user = $userMapper->findByEmail($email);
    if (!empty($user->getUid())) {
      throw new Core\ApiException("User already exists: $email", 6, $this->id, 417);
    }
    $invite = $inviteMapper->findByEmail($email);
    if (!empty($invite->getIid())) {
      throw new Core\ApiException("User already invited: $email", 6, $this->id, 417);
    }

    $token = Core\Utilities::random_string(16);
    $invite->setEmail($email);
    $invite->setToken($token);
    $inviteMapper->save($invite);

    return $token;
  }
}
