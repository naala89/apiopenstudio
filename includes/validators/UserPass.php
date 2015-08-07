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

class UserPass extends Processors\ProcessorBase {

  protected $required = array('user', 'pass');
  protected $details = array(
    'name' => 'Login',
    'description' => 'Validate the request, based on username/password.',
    'menu' => 'validator',
    'input' => array(
      'user' => array(
        'description' => 'The username to validate.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'pass' => array(
        'description' => 'The password to validate.',
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
    Core\Debug::message('Processor Login');
    $this->validateRequired();

    $user = $this->getVar($this->meta->user);
    $pass = $this->getVar($this->meta->pass);
    $cid = $this->request->client;

    $sql = 'SELECT * FROM users WHERE cid=? AND username=? AND password=? AND active=1';
    $bindParams = array($cid, $user, $pass);
    $recordSet = $this->request->db->Execute($sql, $bindParams);
    return $recordSet && $recordSet->RecordCount() > 0;
  }
}
