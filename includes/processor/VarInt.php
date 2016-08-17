<?php

/**
 * variable type integer
 *
 * This is a special case, we cannot use val(), because it validates type before it can be cast.
 * thus get vars, etc will always fail.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarInt extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Integer)',
    'machineName' => 'varInt',
    'description' => 'An integer variable. It validates the input and returns an error if it is not a integer.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarInt', 4);

    Core\Debug::variable($this->val('value'), 'initial val');

    return filter_var($this->val('value'), FILTER_VALIDATE_INT);
  }
}
