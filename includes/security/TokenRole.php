<?php

/**
 * Provide token authentication based on token in DB and the user's role
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

class TokenRole extends Token {

  public $details = array(
    'machineName' => 'tokenRole',
    'name' => 'Token (Role)',
    'description' => 'Validate the request, requiring the consumer to have a valid token and a declared role.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'inputs' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1),
        'accepts' => array('processor')
      ),
      'role' => array(
        'description' => 'The consumers role.',
        'cardinality' => array(1),
        'accepts' => array('processor', 'string')
      )
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    $this->role = $this->val($this->meta->role);
    parent::process();
  }
}
