<?php

/**
 * Perform merge of two external sources
 *
 * METADATA
 * {
 *    "type":"stringTransform",
 *    "meta":{
 *      "transformType":[replace]
 *      "data":{},
 *      "source":{<string type object>},
 *      },
 *    },
 *  },
 * }
 */

include_once(Config::$dirIncludes . 'processor/class.ProcessorString.php');

class ProcessorStringTransform extends ProcessorString
{
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorStringTransform', 4);
    $this->status = 200;
    if (empty($this->meta) || empty($this->meta->source)) {
      $this->status = 417;
      return new Error(1, 'Invalid or empty source.');
    } elseif (empty($this->meta->transformType)) {
      $this->status = 417;
      return new Error(1, 'Empty transformType');
    }

    $processor = $this->getProcessor($this->meta->source);
    if ($this->status != 200) {
      return $processor;
    }
    Debug::message('we have processor');
    $string = $processor->process();
    $this->status = $processor->status;
    if ($this->status != 200) {
      return $processor;
    }
    Debug::variable($string, 'string');
    if (!is_string($string)) {
      return $string;
    }

    $transformType = ucfirst(trim($this->meta->transformType));
    $method = "_transform$transformType";
    if (method_exists($this, $method)) {
      $result = $this->$method($string, $this->meta);
    } else {
      $this->status = 407;
      $result = new Error(-1, "Invalid transformType: $transformType");
    }

    return $result;
  }

  private function _transformReplace($string, $meta)
  {
    Debug::variable($meta, 'meta');
    if (!isset($meta->data)) {
      return new Error(-1, 'Missing or invalid data');
    }

    foreach ($meta->data as $key => $val) {
      if (strpos($string, $key) !== FALSE) {
        $string = str_replace($key, $val, $string);
      }
    }

    return $string;
  }
}
