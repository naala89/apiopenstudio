<?php

namespace Datagator\Output;

class Plain extends Text
{
  protected $header = 'Content-Type:text/plain';
  protected $details = array(
    'name' => 'Plain',
    'machineName' => 'plain',
    'description' => 'Output in plain-text format.',
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
    $objTmp = (object) array('aFlat' => array());
    array_walk_recursive($aNonFlat, create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $objTmp);
    return $objTmp['aFlat'];
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromJson(& $data) {
    return $data;
  }
}