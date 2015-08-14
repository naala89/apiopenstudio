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
use Datagator\Core;

class VarBool extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Boolean)',
    'description' => 'A boolean variable. It validates the input and returns an error if it is not a boolean.',
    'menu' => 'variables',
    'client' => 'all',
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
    Core\Debug::variable($this->meta, 'Processor VarBool', 4);
    $var = parent::process();

    if (!is_bool($var)) {
      throw new Core\ApiException('invalid boolean', 5, $this->id, 417);
    }

    return $var;
  }
}
