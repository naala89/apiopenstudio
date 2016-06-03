<?php

/**
 * Variable type float.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarFloat extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Float)',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'float')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarFloat');

    $value = parent::process();
    if (is_string($value) && is_numeric($value)) {
      $value = floatval($value);
    }

    if (!is_float($value) && $value !== 0) {
      throw new Core\ApiException("invalid float: $value", 6, $this->id, 417);
    }

    return $value;
  }
}
