<?php

/**
 * Variable type boolean.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarBool extends VarMixed
{
  private $accetableStrings = array('yes', 'no', 'true', 'false', '0', '1');
  protected $details = array(
    'name' => 'Var (Boolean)',
    'description' => 'A boolean variable. It validates the input and returns an error if it is not a boolean. Possible input',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    return parent::process();
  }
}
