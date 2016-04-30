<?php

/**
 * variables that are stored in the vars table in the db
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

class VarStorePersistent extends ProcessorBase
{
  protected $details = array(
    'name' => 'Var (Store Persistent)',
    'description' => 'A persistently stored variable. This allows you to store a regularly used variable with a single value and fetch it at any time. The value can be deleted, updated and fetched in future resource and Processor calls.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
      'value' => array(
        'description' => 'The value of the variable. This input is only used in save operations.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal')
      ),
      'operation' => array(
        'description' => 'The operation to be performed on the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"save"', '"delete"', '"fetch"')
      ),
      'strict' => array(
        'description' => 'If set to 0 then return null if var does not exists. If set to 1 throw exception if var does not exist. Default is strict.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', '"0"', '"1"')
      ),
    ),
  );

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarStorePersistent', 4);

    $name = $this->val($this->meta->name);
    $strict = !empty($this->meta->strict) ? $this->val($this->meta->strict) : 1;
    $operation = $this->val($this->meta->operation);
    $mapper = new Db\VarsMapper($this->request->db);
    $var = $mapper->findByAppIdName($this->request->appId, $name);
    if ($strict && empty($var->getId())) {
      throw new Core\ApiException("var $name does not exist", 6, $this->id, 417);
    }

    switch($operation) {
      case 'save':
        $val = $this->val($this->meta->value);
        if ($var->getId() === NULL) {
          $var->setName($name);
          $var->setAppId($this->request->appId);
        }
        $var->setVal($val);
        return TRUE;
        break;
      case 'delete':
        if ($var->getId() === NULL) {
          throw new Core\ApiException('could not delete variable, does not exist', 6, $this->id, 417);
        }
        return $mapper->delete($var);
        break;
      case 'fetch':
        return $var->getVal();
        break;
      default:
        throw new Core\ApiException("invalid operation: $operation", 6, $this->id, 417);
        break;
    }
  }
}
