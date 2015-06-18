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

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorField extends Processor
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
    Debug::message('ProcessorField');
    $this->validateRequired();

    $name = $this->getVar($this->meta->name);
    $value = $this->getVar($this->meta->value);

    return array($name => $value);
  }
}
