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
use Datagator\Core;

class VarStr extends VarMixed
{
  public $details = array(
    'name' => 'Var (String)',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'str')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarStr', 4);
    $value = parent::process();

    if (!is_string($value)) {
      throw new Core\ApiException('invalid string', 5, $this->id, 417);
    }

    return $value;
  }
}
