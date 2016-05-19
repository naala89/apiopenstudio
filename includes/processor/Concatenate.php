<?php

/**
 * Perform string concatenation of two or more inputs
 */

namespace Datagator\Processor;
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
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Concatenate', 4);

    $result = '';
    foreach ($this->meta->sources as $source) {
      $val = $this->val($source);
      $result .= (string)$val;
    }
    Core\Debug::variable($result, 'concatenation result', 4);

    return $result;
  }
}
