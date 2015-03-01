<?php

/**
 * Variable type string
 *
 * METADATA
 * {
 *    "type":"string",
 *    "meta":{
 *      "string":<string>,
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

    if (!$this->isString()) {
      $this->status = 417;
      return new Error(1, 'Invalid or empty string.');
    }

    return $this->meta->data;
  }
}