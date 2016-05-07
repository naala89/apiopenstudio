<?php

/**
 * Provide token authentication based on token
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

class TokenUser extends Token {
  protected $role = false;
  protected $details = array(
    'machineName' => 'tokenUser',
    'name' => 'Token (User)',
    'description' => 'Validate the request by user and token, only allowing specific users to use the resource.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor')
      ),
      'usernames' => array(
        'description' => "The username/s.",
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'literal', 'array'),
      ),
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenUser', 4);

    $token = $this->val($this->meta->token);
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    // check user exists
    $this->request->userInterface->validateToken($token);

    // check user is in the list of valid users
    $usernames = $this->val($this->meta->usernames);
    if (!is_array($usernames)) {
      $usernames = array($usernames);
    }
    $user = $this->request->userInterface->getUser();
    foreach ($usernames as $username) {
      if ($username != $user->getUsername()) {
        throw new Core\ApiException('permission denied', 4, $this->id, 401);
      }
    }

    return true;
  }
}
