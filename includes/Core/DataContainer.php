<?php

namespace Datagator\Core;

class DataContainer extends Entity
{
  /**
   * @var array
   */
  private $types = array(
    'text',
    'xml',
    'json',
    'array'
  );
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
   * @param $type
   */
  public function __construct($data, $type)
  {
    $this->data = $data;
    $this->setType($type);
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

  /**
   * @return mixed
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param $val
   * @throws \Datagator\Core\ApiException
   */
  public function setType($val)
  {
    if (!in_array($val, $this->types)) {
      throw new ApiException("trying to to set an invalid type: $val");
    }
    $this->type = $val;
  }

  /**
   * @return mixed
   */
  public function getTypes()
  {
    return $this->types;
  }
}