<?php

include_once(Config::$dirIncludes . 'output/class.Output.php');

class OutputJson extends Output
{

  public function OutputJson($status, $data)
  {
    parent::__construct($status, $data);
    header('Content-Type:application/json');
  }

  public function process()
  {
    parent::process();
    return json_encode($this->data);
  }
}