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

namespace Datagator\Validator;
use Datagator\Core;
use Datagator\Processor;

class TokenSysAdmin extends Token {

  protected $role = 'sys-admin';
  public $details = array(
    'name' => 'Token Sys-admin',
    'description' => 'Validate the request, based on a token and ensure user has sys-admin role access.',
    'menu' => 'validator',
    'client' => 'System',
    'input' => array(
      'token' => array(
        'description' => 'The token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );
}
