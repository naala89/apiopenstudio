<?php

namespace Datagator\Core;

abstract class DataEntity
{
  /**
   * Data type
   * @var
   */
  protected $type = '';
  /**
   * Data
   * @var mixed
   */
  protected $data;

  /**
   * @param $data
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * @return string
   */
  public function type()
  {
    return $this->type;
  }

  /**
   * @return mixed
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * @param $val
   */
  public function setData($val)
  {
    $this->data = $val;
  }
}