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

class TokenSysAdmin extends Token {

  protected $role = 'sys-admin';
  protected $details = array(
    'machineName' => 'tokenSysAdmin',
    'name' => 'Token (Sys-Admin)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of sys-admin.',
    'menu' => 'Security',
    'client' => 'System',
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
    Core\Debug::variable($this->meta, 'Security TokenSysAdmin', 4);
    parent::process();
  }
}
