<?php

/**
 * Perform merge of two external sources
 *
 * METADATA
 * {
 *    "type":"merge",
 *    "meta":{
 *      "mergeType":"union",
 *      "sources":[
 *        {"type":"input","meta":{"url":"http://data1.com"}},
 *        {"type":"input","meta":{"url":"http://data2.com"}},
 *      ]
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorMerge extends Processor
{
  private $_defaultType = 'union';
  protected $required = array('sources');

  public function process()
  {
    Debug::variable($this->meta, 'processorMerge', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $sources = $this->meta->sources;
    $values = array();
    foreach ($sources as $source) {
      $processor = $this->getProcessor($source);
      if ($this->status != 200) {
        return $processor;
      }
      $data = $processor->process();
      if ($processor->status != 200) {
        $this->status = $processor->status;
        return $data;
      }
      $values[] = $data;
    }

    $type = empty($this->meta->meta->mergeType) ? $this->_defaultType : $this->meta->meta->mergeType;
    $type = ucfirst(trim($type));
    $method = "_merge$type";
    if (method_exists($this, $method)) {
      $result = $this->$method($values);
    } else {
      $this->status = 407;
      $result = new Error(3, $this->id, "invalid mergeType: $type");
    }

    return $result;
  }

  private function _mergeNegate($values)
  {
    $result = array_shift($values);
    foreach ($values as $value) {
      $result = array_diff($result, $value);
    }
    return $result;
  }

  private function _mergeUnion($values)
  {
    $result = array_shift($values);
    $result = is_array($result) ? $result : array($result);
    foreach ($values as $value) {
      if (!is_array($value)) {
        $result[] = $value;
      } else {
        $result += $value;
      }
    }
    return $result;
  }

  private function _mergeIntersect($values)
  {
    $result = array_shift($values);
    foreach ($values as $value) {
      $result = array_intersect($result, $value);
    }
    return $result;
  }
}