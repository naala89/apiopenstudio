<?php

/**
 * varliables that are stored in the vars table in the db
 *
 * METADATA
 * {
 *    "type":"varStore",
 *    "meta":{
 *      "id":<integer>,
 *      "operation":"insert|delete|fetch",
 *      "var":<processor|mixed>,
 *      "val":<processor|mixed>, [optional - if operation is insert]
 *    }
 *  }
 *
 * @TODO: Add check in pace to ensure clients cannot flood the system by saving too many vars.
 */

namespace Datagator\Processors;
use Datagator\Core;

class VarStore extends ProcessorBase
{
  protected $required = array('var', 'operation');
  protected $details = array(
    'name' => 'Var (Store)',
    'description' => 'A stored variable. This allows you to store a regularly used variable with a single value and fetch it at any time.',
    'menu' => 'variables',
    'input' => array(
      'operation' => array(
        'description' => 'The operation to be performed on the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"insert"', '"delete"', '"fetch"')
      ),
      'var' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'mixed')
      ),
      'val' => array(
        'description' => 'The value of the variable. This input is only used in insert operations.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'mixed')
      ),
    ),
  );
  private $ops = array('insert', 'delete', 'fetch');

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarStore');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }
    $var = $this->getVar($this->meta->var);
    $operation = $this->getVar($this->meta->operation);
    if (!in_array($operation, $this->ops)) {
      throw new Core\ApiException("invalid operation: $operation", 1, $this->id, 417);
    }

    $method = "_$operation";
    return $this->$method($this->request->client, $var);
  }

  /**
   * Create a new stored var.
   *
   * @param $client
   * @param $var
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _insert($client, $var)
  {
    $val = $this->getVar($this->meta->val);
    $result = $this->_delete($client, $var);
    if ($result === FALSE) {
      throw new Core\ApiException('there was an error deleting old duplicate vars', 2, $this->id, 417);
    }
    $sql = 'INSERT INTO val ("client", "name", "val") VALUES (?, ?, ?)';
    $recordSet = $this->request->db->Execute($sql, array($client, $var, $val));
    if ($recordSet->Affected_Rows() == 0) {
      throw new Core\ApiException('there was an error inserting vars', 2, $this->id, 417);
    }
    return $result;
  }

  /**
   * Delete a stored var.
   *
   * @param $client
   * @param $var
   * @return bool
   */
  private function _delete($client, $var)
  {
    $sql = 'DELETE FROM vars WHERE client=? AND name=?';
    $recordSet = $this->request->db->Execute($sql, array($client, $var));
    return $recordSet->Affected_Rows() > 0;
  }

  /**
   * Fetch a stored var.
   *
   * @param $client
   * @param $var
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _fetch($client, $var)
  {
    $sql = 'SELECT val FROM vars WHERE client=? AND name=?';
    $recordSet = $this->request->db->Execute($sql, array($client, $var));
    if ($recordSet->RecordCount() < 1) {
      throw new Core\ApiException('there was an error fetching ' . $var, 2, $this->id, 417);
    }
    return $recordSet->fields['val'];
  }
}