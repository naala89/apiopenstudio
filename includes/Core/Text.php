<?php

namespace Datagator\Core;

class Text extends DataEntity
{
  /**
   * Data type
   * @var
   */
  protected $type = 'text';

  /**
   * @param $data
   */
  public function __construct($data)
  {
    parent::__construct($data);
  }
}