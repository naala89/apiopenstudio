<?php

/**
 * Variable type string
 *
 * METADATA
 * {
 *    "type":"string",
 *    "meta":{
 *      "var":<processor|string>,
 *    }
 *  }
 */

namespace Datagator\Processors;

class VariableStr extends \Processor
{
  protected $details = array(
    'name' => 'Var (String)',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'str')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarStr', 4);
    $result = parent::process();

    if (!is_string($result)) {
      throw new \Datagator\includes\ApiException('invalid string', 5, $this->id, 417);
    }

    return $result;
  }
}
