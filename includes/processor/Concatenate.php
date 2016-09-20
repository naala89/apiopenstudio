<?php

/**
 * Perform string concatenation of two or more inputs
 */

namespace Datagator\Processor;
use Datagator\Core;

class Concatenate extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Concatenate',
    'machineName' => 'concatenate',
    'description' => 'Concatenate a series of strings or numbers into a single string.',
    'menu' => 'Operation',
    'application' => 'Common',
    'input' => array(
      'sources' => array(
        'description' => 'The values to concatenate',
        'cardinality' => array(2, '*'),
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
    Core\Debug::variable($this->meta, 'Processor Concatenate', 4);

    $sources = $this->val('sources');
    $result = '';
    foreach ($sources as $source) {
      $result .= (string) $this->isDataEntity($source) ? $source->getData() : $source;
    }

    return new Core\DataContainer($result, 'text');
  }
}
