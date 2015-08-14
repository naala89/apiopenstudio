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

namespace Datagator\Validators;
use Datagator\Core;
use Datagator\Processors;

class ValidateToken extends Processors\ProcessorBase {

  protected $required = array('token');
  protected $details = array(
    'name' => 'ValidateToken',
    'description' => 'Validate the request, based on a token.',
    'menu' => 'validator',
    'input' => array(
      'token' => array(
        'description' => 'The token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process() {
    Core\Debug::message('Validator ValidateToken', 4);
    $this->validateRequired();

    $token = $this->getVar($this->meta->token);
    if (empty($token)) {
      return FALSE;
    }
    $appId = $this->request->appId;

    $sql = 'SELECT * FROM users WHERE client=? AND token=? AND (stale_time > now() OR stale_time IS NULL) AND active=1';
    $bindParams = array($appId, $token);
    $recordSet = $this->request->db->Execute($sql, $bindParams);
    return $recordSet && $recordSet->RecordCount() > 0;
  }
}
