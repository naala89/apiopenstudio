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
    'name' => 'Token',
    'description' => "Validate the api request with a user's token with the role of consumer. This is useful for a private API, and requires a login call from the user to get a token.",
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'input' => array(),
  );
}
