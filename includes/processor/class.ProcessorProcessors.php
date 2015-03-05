<?php

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorProcessors extends Processor
{
  public $displayFrontend = FALSE;

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorProcessors');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $processors = $this->_getProcessors();
    $details = $this->_getDetails($processors);

    return $details;
  }

  private function _getProcessors($dir='processor')
  {
    $path = Config::$dirIncludes . $dir;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    $objects = new RegexIterator($iterator, '/class\.processor[a-z0-9]+\.php/i', RecursiveRegexIterator::GET_MATCH);

    $result = array();
    foreach($objects as $name => $object){
      $result[] = $name;
    }

    return $result;
  }

  private function _getDetails($processors=array())
  {
    $result = array();

    foreach ($processors as $processor) {
      preg_match('/class\.(.+)\.php$/i', $processor, $className);
      $className = $className[1];
      include_once($processor);
      $obj = new $className($this->meta, $this->request);
      if ($this->_display($obj)) {
        preg_match('/processor(.+)/i', $className, $index);
        $result[$index[1]] = $obj->details();
      }
    }

    return $result;
  }

  private function _display($obj)
  {
    $val = $obj->displayFrontend;
    if (is_bool($val)) {
      return $val;
    }
    if (is_array($val)) {
      return in_array($this->request->client, $val);
    }
    return $this->request->client == $val;
  }
}
