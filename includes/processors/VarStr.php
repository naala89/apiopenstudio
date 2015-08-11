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
  protected $details = array(
    'name' => 'Var (String)',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'variables',
    'client' => 'all',
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
    Core\Debug::message('ProcessorVarStr', 4);
    $var = parent::process();

    if (!is_string($var)) {
      throw new Core\ApiException('invalid string', 5, $this->id, 417);
    }

    return $var;
  }
}
