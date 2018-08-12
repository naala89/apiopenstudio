<?php

/**
 *
 */

namespace Datagator\Processor;
use Datagator\Core;

class Collection extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Collection',
    'machineName' => 'collection',
    'description' => 'Collection contains multiple values, like an array.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'values' => array(
        'description' => 'The values in the collection',
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
    Core\Debug::variable($this->meta, 'Processor Collection', 4);

    $values = $this->val('values', true);

    if (!is_array($values)) {
      return array($values);
    }

    if (!Core\Utilities::is_assoc($values)) {
      return $values;
    }

    $result = array();
    foreach ($values as $key => $value) {
      $data = $this->isDataContainer($value) ? $value->getData() : $value;
      $result[] = array($key => $data);
    }

    return new Core\DataContainer($result, 'array');
  }
}
