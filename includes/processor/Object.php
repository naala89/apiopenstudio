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

namespace Datagator\Processor;
use Datagator\Core;

class Object extends ProcessorBase
{
  public $details = array(
    'name' => 'Object',
    'description' => 'Create a custom object from inputs. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'attributes' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Object', 4);

    $result = array();
    $attributes = $this->getVar($this->meta->attributes);
    foreach ($attributes as $attribute) {
      $val = $this->isProcessor($attribute) ? $this->getVar($attribute) : $attribute;
      $result[] = $val;
    }

    return $result;
  }
}
