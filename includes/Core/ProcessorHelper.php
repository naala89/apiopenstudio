<?php

/**
 *
 */

namespace Datagator\Core;
use Datagator\Security;
use Datagator\Endpoint;
use Datagator\Output;
use Datagator\Processor;

class ProcessorHelper
{
  private $_namespaces = array('Security', 'Endpoint', 'Output', 'Processor');
  /**
   * Return processor namespace and class name string.
   * @param $className
   * @param array $namespaces
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  public function getProcessorString($className, $namespaces=null)
  {
    $namespaces = empty($namespaces) ? $this->_namespaces : $namespaces;
    $className = ucfirst(trim($className));

    foreach ($namespaces as $namespace) {
      $classStr = "\\Datagator\\$namespace\\$className";
      if (class_exists($classStr)) {
        return $classStr;
        break;
      }
    }

    throw new ApiException("unknown function in new resource: $className", 1);
  }

  /**
   * Validate whether an object or array is a processor.
   * @param $obj
   * @return bool
   */
  public function isProcessor($obj)
  {
    return (is_object($obj) && !empty($obj->function)) || (is_array($obj) && !empty($obj['function']));
  }
}