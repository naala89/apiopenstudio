<?php

/**
 * Provide token authentication based on token in DB
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
use Datagator\Processor;

class TokenDeveloper extends Token {

  protected $role = 'developer';
  protected $details = array(
    'machineName' => 'tokenDeveloper',
    'name' => 'Token (Developer)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of developer.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor')
      )
    ),
  );

  public function process() {
    Core\Debug::variable($this->meta, 'Security TokenDeveloper', 4);
    $roles = parent::process();
    foreach ($roles as $role) {
      if ($role->getRid() == $this->role->getRid()) {
        return true;
      }
    }
    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
