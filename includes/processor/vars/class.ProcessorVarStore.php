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
 * @TODO: Rename class ProcessorVar to ProcessorVarMixed.
 * @TODO: Add check in pace to ensure clients cannot flood the system by saving too many vars.
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarStore extends ProcessorVar
{
  protected $required = array(
    'var',
    'operation'
  );

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

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorVarStore');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }
    $var = $this->getVar($this->meta->var);
    $operation = $this->getVar($this->meta->operation);
    if (!in_array($operation, $this->ops)) {
      throw new ApiException("invalid operation: $operation", 1, $this->id, 417);
    }

    $method = "_$operation";
    return $this->$method($this->request->client, $var);
  }

  private function _insert($client, $var)
  {
    $val = $this->getVar($this->meta->val);
    $result = $this->_delete($client, $var);
    if (!$result) {
      throw new ApiException('there was an error deleting old duplicate vars', 2, $this->id, 417);
    }
    $result = $this->request->db
        ->insert('vars')
        ->set(array(
            array('client', $client),
            array('name', $var),
            array('val', $val),
        ))
        ->execute();
    if (!$result) {
      throw new ApiException('there was an error inserting vars', 2, $this->id, 417);
    }
    return $result;
  }

  private function _delete($client, $var)
  {
    return $this->request->db
        ->delete('vars')
        ->where(array('client', $client))
        ->where(array('name', $var))
        ->execute();
  }

  private function _fetch($client, $var)
  {
    $result = $this->request->db
        ->select('val')
        ->from('vars')
        ->where(array('client', $client))
        ->where(array('name', $var))
        ->execute();
    $row = $result->fetch_object();
    return !empty($row->val) ? $row->val : FALSE;
  }
}