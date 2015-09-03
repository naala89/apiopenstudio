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

    $classes = [];
    $classes = array_merge($classes, $this->_getClassList('endpoint'));
    $classes = array_merge($classes, $this->_getClassList('output'));
    $classes = array_merge($classes, $this->_getClassList('processor'));
    $classes = array_merge($classes, $this->_getClassList('validator'));

    $result = $this->_getDetails($classes);

    return $result;
  }

  /**
   * @param $namespace
   * @return array
   */
  private function _getClassList($namespace)
  {
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../' . strtolower($namespace)));
    $objects = new \RegexIterator($iterator, '/[a-z0-9]+\.php/i', \RecursiveRegexIterator::GET_MATCH);
    $result = array();
    foreach($objects as $name => $object) {
      preg_match('/([a-zA-Z0-9]+)\.php$/i', $name, $className);
      $result[] = 'Datagator\\' . ucfirst($namespace) . '\\' . $className[1];
    }
    return $result;
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
      $abstractClass = new \ReflectionClass($processor);
      if (!$abstractClass->isAbstract()) {
        $obj = new $processor($this->meta, $this->request);
        if (!empty($obj->details) && !empty($obj->details['application']) && $this->request->user->hasRole($obj->details['application'], 'developer')) {
          $result[] = $obj->details;
        }
      }
    }

    return $result;
  }
}
