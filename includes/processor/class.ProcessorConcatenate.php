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

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorConcatenate extends Processor
{
  protected $required = array('sources');

  protected $details = array(
    'name' => 'Concatenate',
    'description' => 'Concatenate a series of strings or numbers into a single value.',
    'menu' => 'processes',
    'input' => array(
      'sources' => array(
        'description' => 'The values to concatenate',
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'mixed')),
    ),
  );

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorConcatenate', 4);
    $this->validateRequired();

    $result = '';
    foreach ($this->meta->sources as $source) {
      $val = $this->getVar($source);
      $result .= $val;
    }
    Debug::variable($result, 'concatenation result');

    return $result;
  }
}
