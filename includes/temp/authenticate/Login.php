<?php

namespace Datagator\includes\processor;

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
 *
 * @TODO: Can we set ValidateToken so that it can
 */

class Login extends \Processor {
  protected $required = array('token');

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process() {
    Debug::message('ProcessorValidateToken');
    $this->validateRequired();

    $token = $this->getVar($this->meta->token);
    $cid = $this->request->client;

    $sql = 'SELECT * FROM users WHERE client=? AND token=? AND (stale_time > now() OR stale_time IS NULL)';
    $bindParams = array($cid, $token);
    $recordSet = $this->request->db->Execute($sql, $bindParams);
    return $recordSet->RecordCount() > 0;
  }
}
