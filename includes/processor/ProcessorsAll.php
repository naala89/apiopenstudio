<?php

namespace Datagator\Processor;
use Datagator\Core;

class ProcessorsAll extends ProcessorBase
{
  public $details = array(
    'name' => 'processors (all)',
    'description' => 'Fetch data on all processors available to your application.',
    'menu' => 'System',
    'application' => 'All',
    'input' => array()
  );

  /**
   * @return array
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ProcessorsAll');

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
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__));
    $objects = new \RegexIterator($iterator, '/[a-z0-9]+\.php/i', \RecursiveRegexIterator::GET_MATCH);
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
  private function _getDetails(array $processors)
  {
    $result = array();

    foreach ($processors as $processor) {
      preg_match('/([a-zA-Z0-9]+)\.php$/i', $processor, $className);
      $class = 'Datagator\\Processor\\' . $className[1];
      $abstractClass = new \ReflectionClass($class);
      if (!$abstractClass->isAbstract()) {
        $obj = new $class($this->meta, $this->request);
        if (!empty($obj->details) && !empty($obj->details['application']) && $this->request->user->hasRole($obj->details['application'], 'developer')) {
          $result[] = $obj->details();
        }
      }
    }

    return $result;
  }
}
