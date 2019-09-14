<?php

namespace Gaterdata\Output;
use Gaterdata\Core;

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
   * @param $data
   * @return string
   */
  protected function fromBoolean(& $data)
  {
    return $data ? 'true' : 'false';
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromInteger(& $data)
  {
    return $data;
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromFloat(& $data)
  {
    return $data;
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromXml(& $data) {
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromHtml(& $data) {
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromText(& $data) {
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromArray(& $data) {
    return json_encode($data);
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromJson(& $data) {
    return $data;
  }

  protected function fromImage(& $data) {
    return $data;
  }
}
