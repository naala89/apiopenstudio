<?php

/**
 * Simple field type.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Field extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'field',
    'machineName' => 'url',
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
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Field', 4);

    $key = $this->val('key', true);
    $value = $this->val('value', true);

    return new Core\DataContainer(array($key => $value), 'array');
  }
}
