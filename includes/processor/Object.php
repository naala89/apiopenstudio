<?php

/**
 * Simple object type.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Object extends ProcessorEntity {

  protected $details = array(
    'name' => 'Object',
    'description' => 'Create a custom object from inputs. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'attributes' => array(
        'description' => 'The value of an attribute or a complex object.',
        'cardinality' => array(0, '*'),
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
    Core\Debug::variable($this->meta, 'Processor Object', 4);
    $result = array();
    $attributes = $this->val('attributes');

    foreach ($attributes as $attribute) {
      Core\Debug::variable($attribute);
      if (is_array($attribute) && Core\Utilities::is_assoc($attribute) && sizeof($attribute) == 1) {
        $keys = array_keys($attribute);
        $result[$keys[0]] = $attribute[$keys[0]];
      } else {
        $result[] = $attribute;
      }
    }

    return $result;
  }
}
