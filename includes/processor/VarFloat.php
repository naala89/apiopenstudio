<?php

/**
 * Variable type float
 *
 * METADATA
 * {
 *    "type":"varFloat",
 *    "meta":{
 *      "var":<processor|float>,
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarFloat extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Float)',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'float')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarFloat, 4');
    $value = parent::process();

    if (!is_float($value)) {
      throw new Core\ApiException('invalid float', 5, $this->id, 417);
    }

    return $value;
  }
}
