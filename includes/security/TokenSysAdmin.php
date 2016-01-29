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
    'name' => 'Token Sys-admin',
    'description' => 'Validate the request, the user having a role of sys-admin.',
    'menu' => 'validator',
    'client' => 'System',
    'application' => 'All',
    'input' => array(),
  );
}
