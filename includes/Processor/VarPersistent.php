<?php

/**
 * variables that are stored in the vars table in the db
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class VarPersistent extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Persistent)',
    'machineName' => 'var_persistent',
    'description' => 'A persistently stored variable. This allows you to store a regularly used variable with a single value and fetch it at any time. The value can be deleted, updated and fetched in future resource and Processor calls.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'key' => array(
        'description' => 'The key/name of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'value' => array(
        'description' => 'The value of the variable. This input is only used in save operations.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'operation' => array(
        'description' => 'The operation to be performed on the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('save', 'delete', 'fetch'),
        'default' => ''
      ),
      'strict' => array(
        'description' => 'If set to 0 then return null if var does not exist. If set to 1 throw exception if var does not exist. Default is strict. Only used in fetch or delete operations.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  /**
   * @return bool|string
   * @throws \Gaterdata\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $name = $this->val('name');
    $strict = !empty($this->meta->strict) ? $this->val('strict') : 1;
    $operation = $this->val('operation');
    $db = $this->getDb();
    $mapper = new Db\VarsMapper($db);
    $vars = $mapper->findByAppIdName($this->request->appId, $name);

    switch($operation) {
      case 'save':
        $val = $this->val('value');
        if ($vars->getId() === NULL) {
          $vars->setName($name);
          $vars->setAppId($this->request->appId);
        }
        $vars->setVal($val);
        return new Core\DataContainer('true', 'text');
        break;
      case 'delete':
        if (empty($vars->getId())) {
          if ($strict) {
            throw new Core\ApiException('could not delete variable, does not exist', 6, $this->id, 417);
          }
          return new Core\DataContainer('true', 'text');
        }
        return new Core\DataContainer($mapper->delete($vars), 'text');
        break;
      case 'fetch':
        if ($strict && empty($vars->getId())) {
          throw new Core\ApiException('could not fetch variable, does not exist', 6, $this->id, 417);
        }
        return new Core\DataContainer($vars->getVal(), 'text');
        break;
      default:
        throw new Core\ApiException("invalid operation: $operation", 6, $this->id, 417);
        break;
    }
  }
}
