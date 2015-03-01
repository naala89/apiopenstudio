<?php

include_once(Config::$dirIncludes . 'output/class.Output.php');

class OutputText extends Output
{

  public function OutputText($status, $data)
  {
    parent::__construct($status, $data);
    header("Content-Type:text/plain");
  }

  public function process()
  {
    parent::process();
    return $this->data;
  }

  protected function setError()
  {
    if (is_object($this->data) && get_class($this->data) == 'Error') {
      $result = $this->data->process();
      $this->data = 'Error (' . $result['error']['code'] . '): ' . $result['error']['message'];
    }
  }
}