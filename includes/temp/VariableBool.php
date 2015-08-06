<?php

/**
 * Variable type boolean
 *
 * METADATA
 * {
 *    "type":"varBool",
 *    "meta":{
 *      "var":<processor|boolean>,
 *    }
 *  }
 */

namespace Datagator\Processors;

class VariablerBool extends \Processor
{
  protected $details = array(
    'name' => 'Var (Boolean)',
    'description' => 'A boolean variable. It validates the input and returns an error if it is not a boolean.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'bool')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarBool');
    $result = parent::process();

    if (!is_bool($result)) {
      throw new \Datagator\includes\ApiException('invalid boolean', 5, $this->id, 417);
    }

    return $result;
  }
}