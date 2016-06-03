<?php

/**
 * variable type integer
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarInt extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Integer)',
    'description' => 'An integer variable. It validates the input and returns an error if it is not a integer.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'integer')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarInt', 4);

    $value = parent::process();
    if (is_string($value) && is_numeric($value)) {
      $value = $value + 0;
    }

    if (!is_integer($value) && $value !== 0) {
      throw new Core\ApiException("invalid integer: $value", 6, $this->id, 417);
    }

    return $value;
  }
}
