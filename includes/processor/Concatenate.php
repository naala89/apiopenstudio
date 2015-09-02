<?php

/**
 * Perform merge of two external sources
 *
 * METADATA
 * {
 *    "type":"concatenate",
 *    "meta":{
 *      "sources":[
 *        <processor|string|obj>,
 *        <processor|string|obj>,
 *      ]
 *    },
 *  },
 * }
 */

namespace Datagator\Processor;
use Datagator\Core;

class Concatenate extends ProcessorBase
{
  protected $required = array('sources');
  public $details = array(
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
    $this->validateRequired();

    $result = '';
    foreach ($this->meta->sources as $source) {
      $val = $this->getVar($source);
      $result .= $val;
    }
    Core\Debug::variable($result, 'concatenation result', 4);

    return $result;
  }
}