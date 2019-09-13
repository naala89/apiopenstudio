<?php

namespace Gaterdata\Core;
use Gaterdata\Security;
use Gaterdata\Endpoint;
use Gaterdata\Output;
use Gaterdata\Processor;

class ProcessorHelper
{
  private $_namespaces = array('Security', 'Endpoint', 'Output', 'Processor', 'Core');
  /**
   * Return processor namespace and class name string.
   * @param $className
   * @param array $namespaces
   * @return string
   * @throws \Gaterdata\Core\ApiException
   */
  public function getProcessorString($className, $namespaces=null)
  {
    if (empty($className)) {
      throw new ApiException('empty function name', 1, -1, 406);
    }
    $namespaces = empty($namespaces) ? $this->_namespaces : $namespaces;
    $className = ucfirst(trim($className));

    foreach ($namespaces as $namespace) {
      $classStr = "\\Gaterdata\\$namespace\\$className";
      if (class_exists($classStr)) {
        return $classStr;
        break;
      }
    }

    throw new ApiException("unknown function: $className", 1, -1, 406);
    exit;
  }

  /**
   * Validate whether an object or array is a processor.
   * @param $obj
   * @return bool
   */
  public function isProcessor(& $obj)
  {
    if (is_object($obj)) {
      return (isset($obj->function) && isset($obj->id));
    }
    if (is_array($obj)) {
      return (isset($obj['function']) && isset($obj['id']));
    }
    return false;
  }
}