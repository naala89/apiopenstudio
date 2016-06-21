<?php

/**
 * Fragment.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Fragment extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Fragment',
    'description' => 'Insert the result of a fragment declaration.',
    'menu' => 'Common',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the fragment',
        'cardinality' => array(1, 1),
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
