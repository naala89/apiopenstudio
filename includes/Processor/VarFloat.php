<?php

/**
 * Variable type float.
 *
 * This is a special case, we cannot use val(), because it validates type before it can be cast.
 * thus get vars, etc will always fail.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarFloat extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Float)',
    'machineName' => 'varPost',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('float'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarFloat', 4);

    $result = $this->val('value');
    if (!$this->isDataContainer($result)) {
      $result = new Core\DataContainer($result, 'float');
    }
    $float = filter_var($result->getData(), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    if (is_null($float)) {
      throw new Core\ApiException($result->getData() . ' is not float', 0, $this->id);
    }
    $result->setData($float);
    $result->setType('float');
    return $result;
  }
}
