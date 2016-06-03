<?php

/**
 * Perform string concatenation of two or more inputs
 */

namespace Datagator\Processor;
use Codeception\Util\Debug;
use Datagator\Core;

class Concatenate extends ProcessorBase
{
  protected $details = array(
    'name' => 'Concatenate',
    'description' => 'Concatenate a series of strings or numbers into a single value.',
    'menu' => 'Operation',
    'application' => 'All',
    'input' => array(
      'sources' => array(
        'description' => 'The values to concatenate',
        'cardinality' => array(2, '*'),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Concatenate', 4);

    $sources = $this->val($this->meta->sources);
    $result = '';
    foreach ($sources as $source) {
      $result .= (string)$source;
    }

    return $result;
  }
}
