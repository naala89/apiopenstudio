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

namespace Datagator\Processors;
use Datagator\Core;

class ValidateToken extends Processor {
  protected $required = array('token');

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'ProcessorValidateToken');
    $this->validateRequired();

    $token = $this->getVar($this->meta->token);

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
