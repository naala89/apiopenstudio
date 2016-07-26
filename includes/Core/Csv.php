<?php

namespace Datagator\Core;

class Csv extends DataEntity
{
  /**
   * Data type
   * @var
   */
  protected $type = 'csv';

  /**
   * @param $data
   */
  public function __construct($data)
  {
    parent::__construct($data);
  }
}