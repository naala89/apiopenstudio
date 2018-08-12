<?php

/**
 * Variable type random.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarRand extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Rand)',
    'machineName' => 'varRand',
    'description' => 'A random variable. It produces a random variable of any specified length or mix of character types.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'length' => array(
        'description' => 'The length of the variable.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => 8
      ),
      'lower' => array(
        'description' => 'Use lower-case alpha characters.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
      'upper' => array(
        'description' => 'Use upper-case alpha characters.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
      'numeric' => array(
        'description' => 'Use numeric characters.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
      'special' => array(
        'description' => 'Use special characters.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => false
      ),
    ),
  );


  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarRand', 4);

    $length = $this->val('length', true);
    $lower = $this->val('lower', true);
    $upper = $this->val('upper', true);
    $numeric = $this->val('numeric', true);
    $special = $this->val('special', true);

    return new Core\DataContainer(Core\Utilities::random_string($length, $lower, $upper, $numeric, $special), 'text');
  }
}
