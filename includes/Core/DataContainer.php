<?php

namespace Gaterdata\Core;

class DataContainer extends Entity
{
  /**
   * @var array
   */
  private $types = array(
    'boolean',
    'integer',
    'float',
    'text',
    'array',
    'json',
    'xml',
    'image'
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
    $this->setData($data);
    $this->setType($type);
  }

  /**
   * @return mixed
   */
  public function getData()
  {
    if ($this->type == 'boolean') {
      return filter_var($this->data, FILTER_VALIDATE_BOOLEAN);
    }
    if ($this->type == 'integer') {
      return filter_var($this->data, FILTER_VALIDATE_INT);
    }
    if ($this->type == 'float') {
      return filter_var($this->data, FILTER_VALIDATE_FLOAT);
    }
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
   * @throws \Gaterdata\Core\ApiException
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