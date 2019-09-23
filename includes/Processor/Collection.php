<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Defines the Collection class.
 */

class Collection extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Collection',
    'machineName' => 'collection',
    'description' => 'Collection contains multiple values, like an array or list.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => [
      'values' => [
        'description' => 'The values in the collection',
        'cardinality' => [0, '*'],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Collection', 4);

    $values = $this->val('values', true);

    if ($this->isDataContainer($values)) {
      if ($values->getType == 'array') {
        return $values;
      }
      // Convert the container of single type into a container of array.
      return new Core\DataContainer([$values], 'array');
    }

    // Convert single value into an array container.
    if (!is_array($values)) {
      return new Core\DataContainer([$values], 'array');
    }

    // We have an array, keys can be computed to allow dynamic associative arrays.
    $result = [];
    foreach ($values as $key => $value) {
      $key = $this->val($key);
      $value = $this->val($value);
      $result[$key] = $value;
    }
    return new Core\DataContainer($result, 'array');
  }
}
