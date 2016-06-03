<?php

/**
 * Simple field type
 *
 * METADATA
 * {
 *    "type":"field",
 *    "meta":{
 *      "name":<processor|string>,
 *      "value":<processor|var>
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class Field extends ProcessorBase
{
  protected $details = array(
    'name' => 'Field',
    'description' => 'Create a name value pair. This is primarily for use as a field in object.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'key' => array(
        'description' => 'The key of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'value' => array(
        'description' => 'The value of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Field', 4);

    $name = $this->val($this->meta->key);
    $value = $this->val($this->meta->value);

    return array($name => $value);
  }
}
