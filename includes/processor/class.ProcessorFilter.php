<?php

/**
 * Perform input from external source
 *
 * METADATA
 * {
 *    "type": "filter",
 *    "meta": {
 *      "filterType": [add|drop],
 *      "source": {},
 *      "data": {},
 *    },
 * }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorFilter extends Processor
{
  public function process()
  {
    Debug::variable($this->meta, 'processorFilter', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $processor = $this->getProcessor($this->meta->source);
    if ($this->status != 200) {
      return $processor;
    }
    $source = $processor->process();
    if ($processor->status != 200) {
      $this->status = $processor->status;
      return $source;
    }

    $type = ucfirst(trim($this->meta->filterType));
    $method = "_filter$type";
    if (method_exists($this, $method)) {
      $result = $this->$method($source, $this->meta->data);
    } else {
      throw new ApiException("invalid filterType: $type", 3, $this->id, 417);
    }

    return $result;
  }

  private function _filterAdd($source, $data)
  {
    foreach ($data as $key => $value) {
      $source[$key] = $value;
    }
    return $source;
  }

  private function _filterDrop($source, $data)
  {
    foreach ($data as $key => $value) {
      if (isset($source[$key])) {
        unset($source[$key]);
      }
    }
    return $source;
  }
}