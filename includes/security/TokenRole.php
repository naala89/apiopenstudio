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
    'machineName' => 'tokenConsumer',
    'name' => 'Token (Consumer)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a role of consumer.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'inputs' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1),
        'accepts' => array('processor')
      )
    ),
  );
}
