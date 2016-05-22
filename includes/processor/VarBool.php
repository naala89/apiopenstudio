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

namespace Datagator\Processor;
use Datagator\Core;

class VarBool extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Boolean)',
    'description' => 'A boolean variable. It validates the input and returns an error if it is not a boolean. Possible input',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'bool')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarBool', 4);
    $value = parent::process();

    if (is_string($value)) {
      $value = strtolower($value) == 'true' || $value = '1' || strtolower($value) =='yes';
    }

    if (!is_bool($value)) {
      throw new Core\ApiException('invalid boolean', 5, $this->id, 417);
    }

    return $value;
  }
}
