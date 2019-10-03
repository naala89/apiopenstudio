<?php

/**
 * Simple field type.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarField extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (field)',
    'machineName' => 'var_field',
    'description' => 'Create a name value pair. This is primarily for use as a field in object.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'key' => array(
        'description' => 'The key of the nvp.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'value' => array(
        'description' => 'The value of the nvp.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $key = $this->val('key', true);
    $value = $this->val('value', true);

    return new Core\DataContainer(array($key => $value), 'array');
  }
}
