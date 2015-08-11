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
use Datagator\Core;

class VarFloat extends VarMixed
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
    Core\Debug::message('Processor VarFloat, 4');
    $var = parent::process();

    if (!is_float($var)) {
      throw new Core\ApiException('invalid float', 5, $this->id, 417);
    }

    return $var;
  }
}
