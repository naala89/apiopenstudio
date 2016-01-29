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
    'name' => 'Token User',
    'description' => 'Validate the request, and only allow for a user.',
    'menu' => 'validator',
    'client' => 'All',
    'application' => 'All',
    'input' => array(
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
    Core\Debug::variable($this->meta, 'Validator TokenConsumer', 4);

    // check user exists
    $this->request->user->findByToken($this->val($this->meta->token));
    if (!$this->request->user->exists() || !$this->request->user->isActive()) {
      throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }
    // check user is in the list of valid users
    $usernames = $this->val($this->meta->usernames);
    if (!is_array($usernames)) {
      $usernames = array($usernames);
    }
    $user = $this->request->user->getUser();
    foreach ($usernames as $username) {
      if ($username != $user->getUsername()) {
        throw new Core\ApiException('permission denied', 4, $this->id, 401);
      }
    }

    return TRUE;
  }
}
