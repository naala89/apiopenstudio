<?php

/**
 * variable type integer
 *
 * METADATA
 * {
 *    "type":"string",
 *    "meta":{
 *      "data":<string>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/class.ProcessorFieldBase.php');

class ProcessorString extends ProcessorFieldBase
{
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorString', 4);
    $this->status = 200;

    if (!$this->isInteger()) {
      $this->status = 417;
      return new Error(1, 'Invalid or empty integer.');
    }

    return $this->meta->data;
  }
}