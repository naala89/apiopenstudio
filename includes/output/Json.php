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
    $data = $this->data;

    if ($this->isJson($this->data)) {
      return $data;
    }

    if (is_object($data)) {
      $data = (array) $data;
    }

    return json_encode($data);
  }
}
