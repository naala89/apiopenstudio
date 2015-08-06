<?php

namespace includes\json;

class Json extends \includes\output
{
  public function process()
  {
    parent::process();
    if (Config::$debugInterface == 'LOG' || (Config::$debug < 1 && Config::$debugDb < 1)) {
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
