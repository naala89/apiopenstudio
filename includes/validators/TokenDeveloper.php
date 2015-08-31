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

namespace Datagator\Validators;
use Datagator\Core;
use Datagator\Processors;

class TokenDeveloper extends Token {

  protected $role = 'developer';
  public $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, based on a token and ensure user has developer role access.',
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
