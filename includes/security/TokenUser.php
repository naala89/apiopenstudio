<?php

/**
 * Provide token authentication based on token
 */

namespace Datagator\Security;
use Datagator\Core;
use Datagator\Db;
use Datagator\Processor;

class TokenUser extends Processor\ProcessorEntity {
  protected $role = false;
  protected $details = array(
    'name' => 'Token (User)',
    'description' => 'Validate the request by user and token, only allowing specific users to use the resource.',
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
      ),
      'usernames' => array(
        'description' => 'The username/s.',
        'cardinality' => array(1, '*'),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenUser', 4);

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

    // check user is in the list of valid users
    $usernames = $this->val($this->meta->usernames);
    if (!is_array($usernames)) {
      $usernames = array($usernames);
    }
    foreach ($usernames as $username) {
      if ($username == $user->getUsername()) {
        return true;
      }
    }
    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
