<?php

/**
 * Variable type number.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarNum extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Number)',
    'description' => 'A number variable. It validates the input and returns an error if it is not a real number.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('numeric'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarNum', 4);

    return parent::process();
  }
}
