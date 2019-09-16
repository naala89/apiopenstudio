<?php

/**
 * Request and Processor utility class.
 */

namespace Gaterdata\Core;

class Request
{
  private $uri;
  private $accName;
  private $accId;
  private $appName;
  private $appId;
  private $method;
  private $args;
  private $getVars;
  private $postVars;
  private $ip;
  private $outFormat;
  private $resource;
  private $ttl = 0;
  private $fragments = [];

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
  public function setAccName($var)
  {
    $this->accName = $var;
  }

  /**
   * @return mixed
   */
  public function getAccName()
  {
    return $this->accName;
  }

  /**
   * @param $var
   */
  public function setAccId($var)
  {
    $this->accId = $var;
  }

  /**
   * @return mixed
   */
  public function getAccId()
  {
    return $this->accId;
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
  public function setAppId($var)
  {
    $this->appId = $var;
  }

  /**
   * @return mixed
   */
  public function getAppId()
  {
    return $this->appId;
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
}
