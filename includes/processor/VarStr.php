<?php

/**
 * Variable type string.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarStr extends VarMixed
{
  protected $details = array(
    'name' => 'Var (String)',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'string')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarStr', 4);
    $value = parent::process();

    if (!is_string($value)) {
      throw new Core\ApiException('invalid string', 6, $this->id, 417);
    }

    return $value;
  }
}
