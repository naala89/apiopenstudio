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
 *
 * @TODO: Can we set ValidateToken so that it can
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorValidateToken extends Processor {
  protected $required = array('token');

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process() {
    Debug::message('ProcessorValidateToken');
    $this->validateRequired();

    $token = $this->getVar($this->meta->token);
    if (empty($token)) {
      return FALSE;
    }
    $cid = $this->request->client;

    $sql = 'SELECT * FROM users WHERE client=? AND token=? AND (stale_time > now() OR stale_time IS NULL)';
    $bindParams = array($cid, $token);
    $recordSet = $this->request->db->Execute($sql, $bindParams);
    return $recordSet->RecordCount() > 0;
  }
}
