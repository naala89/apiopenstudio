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
 *      "name":<processor|mixed>,
 *      "val":<processor|mixed>, [optional - if operation is insert]
 *    }
 *  }
 *
 * @TODO: Add check in pace to ensure clients cannot flood the system by saving too many vars.
 */

namespace Datagator\Processors;
use Datagator\Core;
use Datagator\Db;

class VarStore extends ProcessorBase
{
  protected $required = array('name', 'operation');
  protected $details = array(
    'name' => 'Var (Store)',
    'description' => 'A stored variable. This allows you to store a regularly used variable with a single value and fetch it at any time.',
    'menu' => 'variables',
    'client' => 'all',
    'input' => array(
      'operation' => array(
        'description' => 'The operation to be performed on the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"save"', '"delete"', '"fetch"')
      ),
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
      'val' => array(
        'description' => 'The value of the variable. This input is only used in save operations.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarStore', 4);
    $this->validateRequired();

    $name = $this->getVar($this->meta->name);
    $operation = $this->getVar($this->meta->operation);
    $mapper = new Db\VarsMapper($this->request->db);
    $var = $mapper->findByAppIdName($this->request->appId, $name);

    switch($operation) {
      case 'save':
        $val = $this->getVar($this->meta->val);
        if ($var->getId() === NULL) {
          $var->setName($name);
          $var->setAppId($this->request->appId);
        }
        $var->setVal($val);
        return TRUE;
        break;
      case 'delete':
        if ($var->getId() === NULL) {
          throw new Core\ApiException('could not delete variable, does not exist');
        }
        return $mapper->delete($var);
        break;
      case 'fetch':
        return $var->getVal();
        break;
      default:
        throw new Core\ApiException("invalid operation: $operation", 1, $this->id, 417);
        break;
    }
  }
}
