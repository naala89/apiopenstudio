<?php

/**
 * Request and Processor utility class.
 */

namespace Datagator\Core;

class Request
{
  private $uri;
  private $appName;
  private $method;
  private $args;
  private $getVars;
  private $postVars;
  private $ip;
  private $outFormat;
  private $resource;
  private $ttl = 0;
  private $fragments = array();

  /**
   * @param $var
   */
  public function setUri($var)
  {
    $this->uri = $var;
  }

  /**
   * @return mixed
   */
  public function getUri()
  {
    return $this->uri;
  }

  /**
   * @param $var
   */
  public function setAppName($var)
  {
    $this->appName = $var;
  }

  /**
   * @return mixed
   */
  public function getAppName()
  {
    return $this->appName;
  }

  /**
   * @param $var
   */
  public function setMethod($var)
  {
    $this->method = $var;
  }

  /**
   * @return mixed
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @param $var
   */
  public function setArgs($var)
  {
    $this->args = $var;
  }

  /**
   * @return mixed
   */
  public function getArgs()
  {
    return $this->args;
  }

  /**
   * @param $var
   */
  public function setGetVars($var)
  {
    $this->getVars = $var;
  }

  /**
   * @return mixed
   */
  public function getGetVars()
  {
    return $this->getVars;
  }

  /**
   * @param $var
   */
  public function setPostVars($var)
  {
    $this->postVars = $var;
  }

  /**
   * @return mixed
   */
  public function getPostVars()
  {
    return $this->postVars;
  }

  public function setIp($var)
  {
    $this->ip = $var;
  }

  /**
   * @return mixed
   */
  public function getIp()
  {
    return $this->ip;
  }

  /**
   * @param $var
   */
  public function setOutFormat($var)
  {
    $this->outFormat = $var;
  }

  /**
   * @return mixed
   */
  public function getOutFormat()
  {
    return $this->outFormat;
  }

  /**
   * @param $var
   */
  public function setResource($var)
  {
    $this->resource = $var;
  }

  /**
   * @return mixed
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * @param $var
   */
  public function setTtl($var)
  {
    $this->ttl = $var;
  }

  /**
   * @return int
   */
  public function getTtl()
  {
    return $this->ttl;
  }

  /**
   * @param $var
   */
  public function setFragments($var)
  {
    $this->fragments = $var;
  }

  /**
   * @return array
   */
  public function getFragments()
  {
    return $this->fragments;
  }

  /**
   * Recursively crawl though metadata. Recurse through Replace all processors with result values and return final value
   * @param $meta
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function crawlMeta(& $meta)
  {
    // array of values - parse each one
    if (is_array($meta)) {
      foreach ($meta as $key => & $value) {
        $value = $this->crawlMeta($value);
      }
    }

    // object of value - process each key/value, and process() if a processpr
    if (is_object($meta)) {
      // replace each value of key/value pair with final value
      foreach ($meta as $key => & $value) {
        $value = $this->crawlMeta($value);
      }
      if (!empty($meta->function) && !empty($meta->id)) {
        $classStr = $this->getProcessor($meta->function);
        \Datagator\Core\Debug::variable($classStr);
        $class = $meta->function == 'fragment' ? new $classStr($meta, $this->fragments) : new $classStr($meta);
        return $class->process();
      }
    }

    return $meta;
  }

  /**
   * Return processor namespace and class name string.
   * @param $className
   * @param array $namespaces
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  public function getProcessor($className, $namespaces=array('Security', 'Endpoint', 'Output', 'Processor'))
  {
    $className = ucfirst(trim($className));
    $class = null;

    foreach ($namespaces as $namespace) {
      $classStr = "\\Datagator\\$namespace\\$className";
      if (class_exists($classStr)) {
        $class = $classStr;
        break;
      }
    }

    if (!$class) {
      throw new ApiException("unknown function in new resource: $className", 1);
    }
    return $classStr;
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
