<?php

namespace Datagator\Output;
use Datagator;

class Json extends Output
{
  protected $header = 'Content-Type: application/json';
  protected $details = array(
    'name' => 'Json',
    'description' => 'Output in JSON format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'A single or array of URLs to send the results to.',
        'cardinality' => array(1, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'accepts' => array('function', '"get"', '"post"'),
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor field'),
      ),
    ),
  );

  /**
   * @return array|null|string
   */
  protected function getData()
  {
    return $this->toJson();
  }

  /**
   * @param null $data
   * @return array|null|string
   */
  protected function toJson($data=null)
  {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = (array) $data;
    }
    if (!$this->isJson($data)) {
      $data = json_encode($data);
    }
    return $data;
  }

  /**
   * @param $string
   * @return bool
   */
  protected function isJson($string)
  {
    if (!is_string($string)) {
      return FALSE;
    }
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }
}
