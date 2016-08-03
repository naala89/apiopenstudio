<?php

/**
 * Variable type boolean.
 *
 * This is a special case, we cannot use val(), because it validates type before it can be cast.
 * thus get vars, etc will always fail.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarBool extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Boolean)',
    'machineName' => 'varBool',
    'description' => 'A boolean variable. It validates the input and returns an error if it is not a boolean. Possible input',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarBool', 4);

    if (empty($this->meta->value)) {
      throw new Core\ApiException('input empty',2 , $this->id, 500);
    }

    $result = $this->meta->value;
    if ($this->_checkBool($result)) {
      return filter_var($result, FILTER_VALIDATE_BOOLEAN);
    }
    throw new Core\ApiException('boolean required',2 , $this->id, 500);
  }

  public function _checkBool($var) {
    return null !== filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }
}
