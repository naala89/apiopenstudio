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

class TokenConsumer extends Token {

  protected $role = 'consumer';
  public $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, based on a token.',
    'menu' => 'validator',
    'client' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );
}
