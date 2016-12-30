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
      'elements' => array(
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

    $elements = $this->val('elements');
    $result = [];
    foreach ($elements as $element) {
      $result[] = $this->isDataContainer($element) ? $this->val($element, true) : $element;
    }

    return new Core\DataContainer($result, 'array');
  }
}
