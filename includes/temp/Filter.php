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

namespace Datagator\Processors;

class Filter extends \Processor
{
  public function process()
  {
    Debug::variable($this->meta, 'processorFilter', 4);
    $this->validateRequired();

    $processor = $this->getProcessor($this->meta->source);
    $source = $processor->process();

    $type = ucfirst(trim($this->meta->filterType));
    $method = "_filter$type";
    if (method_exists($this, $method)) {
      $result = $this->$method($source, $this->meta->data);
    } else {
      throw new \Datagator\includes\ApiException("invalid filterType: $type", 3, $this->id, 417);
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