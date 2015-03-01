<?php

/**
 * Parent class for variable types
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorFieldBase extends Processor
{
  public function isString()
  {
    if (empty($this->meta->data) || !is_string($this->meta->data)) {
      return FALSE;
    }
    return TRUE;
  }

  public function isInteger()
  {
    if (empty($this->meta->data) || !is_integer($this->meta->data)) {
      return FALSE;
    }
    return TRUE;
  }

  public function isFloat()
  {
    if (empty($this->meta->data) || !is_float($this->meta->data)) {
      return FALSE;
    }
    return TRUE;
  }

  public function isBoolean()
  {
    if (empty($this->meta->data) || !is_bool($this->meta->data)) {
      return FALSE;
    }
    return TRUE;
  }
}