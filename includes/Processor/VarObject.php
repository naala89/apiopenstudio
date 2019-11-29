<?php

/**
 * Simple object type.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarObject extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Var (object)',
    'machineName' => 'var_object',
    'description' => 'Create a custom object from inputs. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
    'menu' => 'Primitive',
    'input' => [
      'attributes' => [
        'description' => 'The value of an attribute or a complex object.',
        'cardinality' => [0, '*'],
        'literalAllowed' => true,
        'limitFunctions' => ['varField'],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => '',
      ],
    ],
  ];

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
    $attributes = $this->val('attributes', false);
    $result = [];

    foreach ($attributes as $attribute) {
      $field = $attribute->getData();
      $keys = array_keys($field);
      $result[$keys[0]] = $field[$keys[0]];
    }

    return new Core\DataContainer($result, 'array');
  }
}
