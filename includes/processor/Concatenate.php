<?php

/**
 * Perform string concatenation of two or more inputs
 */

namespace Datagator\Processor;
use Datagator\Core;

class Concatenate extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Concatenate',
    'description' => 'Concatenate a series of strings or numbers into a single value.',
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
      $result .= (string)$source;
    }

    return $result;
  }
}
