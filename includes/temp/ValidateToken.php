<?php

/**
 * Provide token authentication based on token in DB
 *
 * Meta:
 *    {
 *      "type": "tokenValidate",
 *      "meta": {
 *        "id":<integer>,
 *        "token": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Processor;
use Datagator\Core;

class ValidateToken extends Processor {
  public $details = array(
    'name' => 'Validate Token',
    'description' => 'Stores the access details from a users login to a remote drupal site for future use.',
    'menu' => 'drupal',
    'client' => 'all',
    'input' => array(
      'source' => array(
        'description' => 'The results of a login attempt to the remote site. i.e. Processor InputUrl.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor')
      ),
      'externalEntity' => array(
        'description' => 'The name of the external entity this user is tied to (default is "drupal" - use custom names if you access more than one drupal site).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'ProcessorValidateToken');

    $token = $this->val($this->meta->token);

    $result = $this->request->db
      ->select()
      ->from('users', 'stale_time')
      ->where(array('client', $this->request->client))
      ->where(array('token', $this->request->db->escape($token)))
      ->where('(now() < stale_time OR stale_time IS NULL)')
      ->execute();

    return $result->num_rows > 0;
  }
}
