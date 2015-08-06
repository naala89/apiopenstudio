<?php

/**
 * Simple object type.
 *
 * This will usually take ana array of processorType. However, you can also have literals or processors.
 *
 * METADATA
 * {
 *    "type":"object",
 *    "meta":{
 *      "attributes":[
 *        <processor|var|literal>,
 *      ]
 *    }
 *  }
 */

namespace Datagator\Processors;

class Object extends Processor
{
  protected $details = array(
    'name' => 'Object',
    'description' => 'Create a custom object from inputs. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values.',
    'menu' => 'basic',
    'input' => array(
      'attributes' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal', 'var'),
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorObject');

    $result = array();
    $attributes = $this->getVar($this->meta->attributes);
    foreach ($attributes as $attribute) {
      $val = $this->isProcessor($attribute) ? $this->getVar($attribute) : $attribute;
      $result[] = $val;
    }

    return $result;
  }
}
