<?php

namespace Datagator\Outputs;
use Datagator;

class Json extends Output
{
  public function process()
  {
    parent::process();
    if (Datagator\Config::$debugInterface == 'LOG' || (Datagator\Config::$debug < 1 && Datagator\Config::$debugDb < 1)) {
      header('Content-Type: application/json');
    }
    if ($this->isJson($this->data)) {
      return $this->data;
    }
    if (is_object($this->data)) {
      $this->data = (array) $this->data;
    }
    return json_encode($this->data);
  }
}
