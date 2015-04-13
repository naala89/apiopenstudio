<?php

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorSystem extends Processor
{
  public $displayFrontend = FALSE;

  public function process()
  {
    if ($this->request->client != 7) {
      throw new ApiException('permission denied', 4, $this->id, 307);
    }
    return TRUE;
  }
}
