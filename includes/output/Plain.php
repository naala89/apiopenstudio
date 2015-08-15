<?php

namespace Datagator\Outputs;

class Plain extends Output
{
  public function process()
  {
    parent::process();
    header('Content-Type:text/plain');
    return $this->data;
  }
}