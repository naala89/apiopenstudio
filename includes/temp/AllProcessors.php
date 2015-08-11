<?php

namespace Datagator\Processors;

class AllProcessors extends ProcessorBase
{
  public $displayFrontend = FALSE;
  protected $details = array(
    'name' => 'All processors',
    'description' => 'Fetch data on all public and client processors available.',
    'menu' => 'system',
    'input' => array()
  );

  /**
   * @return array
   */
  public function process()
  {
    Debug::variable($this->meta, 'Processor AllProcessors');

    $processors = $this->_getProcessors();
    $details = $this->_getDetails($processors);

    return $details;
  }

  /**
   * Get list of processors.
   *
   * @return array
   */
  private function _getProcessors()
  {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
    $objects = new RegexIterator($iterator, '/[a-z0-9]+\.php/i', RecursiveRegexIterator::GET_MATCH);

    $result = array();
    foreach($objects as $name => $object){
      $result[] = $name;
    }

    return $result;
  }

  /**
   * Get details of all processors in an array.
   *
   * @param array $processors
   * @return array
   */
  private function _getDetails($processors=array())
  {
    $result = array();

    foreach ($processors as $processor) {
      preg_match('/(.+)\.php$/i', $processor, $className);
      $className = $className[1];
      $obj = new $className($this->meta, $this->request);
      if ($this->_display($obj)) {
        preg_match('/(.+)/i', $className, $index);
        $result[$index[1]] = $obj->details();
      }
    }

    return $result;
  }

  /**
   * @param $obj
   * @return bool
   */
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
