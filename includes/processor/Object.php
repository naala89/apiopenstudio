<?php

/**
 * Simple object type.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Object extends ProcessorBase {

  protected $details = array(
    'name' => 'Object',
    'description' => 'Create a custom object from inputs. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'attributes' => array(
        'description' => 'The value of an attribute or a complex object.',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process() {
    $result = array();
    $attributes = $this->val($this->meta->attributes);

    if (sizeof($attributes) == 1) {
      $result = $attributes[0];
    } else {
      foreach ($attributes as $attribute) {
        $result[] = $attribute;
      }
    }

    return $result;
  }
}
