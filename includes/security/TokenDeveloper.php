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
    'name' => 'Token',
    'description' => 'Validate the request, the user having a role of developer.',
    'menu' => 'validator',
    'client' => 'System',
    'application' => 'All',
    'input' => array(),
  );
}
