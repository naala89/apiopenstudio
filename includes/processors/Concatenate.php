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

namespace Datagator\Processors;
use Datagator\Core;

class Concatenate extends ProcessorBase
{
  protected $required = array('sources');
  protected $details = array(
    'name' => 'Concatenate',
    'description' => 'Concatenate a series of strings or numbers into a single value.',
    'menu' => 'processes',
    'client' => 'all',
    'input' => array(
      'sources' => array(
        'description' => 'The values to concatenate',
        'cardinality' => array(2, '*'),
        'accepts' => array('processor', 'var', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Concatenate', 4);

    $result = '';
    foreach ($this->meta->sources as $source) {
      $val = $this->getVar($source);
      $result .= $val;
    }
    Core\Debug::variable($result, 'concatenation result', 4);

    return $result;
  }
}
