<?php

namespace Datagator\Core;

class Json extends DataEntity
{
  /**
   * Data type
   * @var
   */
  protected $type = 'json';

  /**
   * @param $data
   */
  public function __construct($data)
  {
    parent::__construct($data);
  }
}