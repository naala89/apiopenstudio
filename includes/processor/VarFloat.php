<?php

/**
 * Variable type float.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarFloat extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Float)',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array('float'),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarFloat');

    return parent::process();
  }
}
