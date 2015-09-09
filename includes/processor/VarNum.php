<?php

/**
 * Variable type number
 *
 * METADATA
 * {
 *    "type":"varNum",
 *    "meta":{
 *      "var":<processor|number>,
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarNum extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Number)',
    'description' => 'A number variable. It validates the input and returns an error if it is not a real number.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'num')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarNum', 4);
    $value = parent::process();

    if (!is_numeric($value)) {
      throw new Core\ApiException('invalid number', 5, $this->id, 417);
    }

    return $value;
  }
}
