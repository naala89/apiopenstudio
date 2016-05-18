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

class TokenConsumer extends Token {

  protected $role = 'consumer';
  protected $details = array(
    'name' => 'Token (Consumer)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of consumer for application referenced by the appId in the URI.',
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
    Core\Debug::variable($this->meta, 'Security TokenConsumer', 4);

    $roles = parent::process();

    foreach ($roles as $role) {
      if ($role->getRid() == $this->role->getRid()) {
        return true;
      }
    }

    throw new Core\ApiException('permission denied', 4, $this->id, 401);
  }
}
