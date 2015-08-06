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

namespace Datagator\Processors;
use Datagator\Core;

class Field extends Processor
{
  protected $details = array(
    'name' => 'Field',
    'description' => 'Create a name value pair. This is primarily for use as a field in object.',
    'menu' => 'basic',
    'input' => array(
      'name' => array(
        'description' => 'The name of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
      'value' => array(
        'description' => 'The value of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::message('ProcessorField', 4);
    $this->validateRequired();

    $name = $this->getVar($this->meta->name);
    $value = $this->getVar($this->meta->value);

    return array($name => $value);
  }
}
