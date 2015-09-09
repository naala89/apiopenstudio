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
  protected $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, the user having a role of consumer.',
    'menu' => 'validator',
    'client' => 'All',
    'application' => 'All',
    'input' => array(),
  );
}
