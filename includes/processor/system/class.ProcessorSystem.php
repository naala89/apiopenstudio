<?php

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorSystem extends Processor
{
  public $displayFrontend = FALSE;

  public function process()
  {
    if ($this->request->client != 7) {
      $this->status = 307;
      return new Error(4, $this->id, 'permission denied');
    }
    return TRUE;
  }
}
