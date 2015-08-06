<?php

//include_once(Config::$dirIncludes . 'output/class.OutputText.php');

class Plain extends \Text
{
  public function process()
  {
    parent::process();
    header('Content-Type:text/plain');
    return $this->data;
  }
}