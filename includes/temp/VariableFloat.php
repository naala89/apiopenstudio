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

namespace Datagator\Processors;

class VariableFloat extends \Processor
{
  protected $details = array(
    'name' => 'Var (Float)',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'float')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarFloat');
    $result = parent::process();

    if (!is_float($result)) {
      throw new \Datagator\includes\ApiException('invalid float', 5, $this->id, 417);
    }

    return $result;
  }
}
