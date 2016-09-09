<?php

namespace Datagator\Output;
use Datagator\Core;

class Text extends Output
{
  protected $header = 'Content-Type:text/text';
  protected $details = array(
    'name' => 'Text',
    'machineName' => 'text',
    'description' => 'Output in text format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'Destination URLs for the output.',
        'cardinality' => array(1, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post'),
        'default' => ''
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array('field'),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  /**
   * @return array|null|string
   */
  protected function getData()
  {
    Core\Debug::variable('Processor Text->getData()');
    return $this->toText($this->data->getData());
  }

  /**
   * @param null $data
   * @return array|null|string
   */
  protected function toText($data=null)
  {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = (array) $data;
    }
    if (is_array($data)) {
      $data = serialize($data);
    }
    return $data;
  }
}