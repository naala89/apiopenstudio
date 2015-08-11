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

class Field extends ProcessorBase
{
  protected $required = array('name', 'value');
  protected $details = array(
    'name' => 'Field',
    'description' => 'Create a name value pair. This is primarily for use as a field in object.',
    'menu' => 'basic',
    'client' => 'all',
    'input' => array(
      'name' => array(
        'description' => 'The name of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'value' => array(
        'description' => 'The value of the nvp.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::message('Processor Field', 4);

    $name = $this->getVar($this->meta->name);
    $value = $this->getVar($this->meta->value);

    return array($name => $value);
  }
}
