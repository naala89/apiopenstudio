<?php

include_once(Config::$dirIncludes . 'output/class.OutputText.php');

class OutputPlain extends OutputText
{
  public function process()
  {
    parent::process();
    header('Content:text/plain');
    return $this->data;
  }
}