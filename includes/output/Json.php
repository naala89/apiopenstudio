<?php

namespace Datagator\Output;
use Datagator;

class Json extends Output
{
  protected $header = 'Content-Type: application/json';
  public $details = array(
    'name' => 'Json',
    'description' => 'Output in JSON format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(),
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
