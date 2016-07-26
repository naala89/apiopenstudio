<?php

namespace Datagator\Core;

class Xml extends DataEntity
{
  /**
   * Data type
   * @var
   */
  protected $type = 'xml';

  /**
   * @param $data
   */
  public function __construct($data)
  {
    parent::__construct($data);
  }
}