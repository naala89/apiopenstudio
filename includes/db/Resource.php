<?php

/**
 * Container for data for a resource row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Resource
{
  protected $id;
  protected $appId;
  protected $method;
  protected $identifier;
  protected $meta;
  protected $ttl;

  /**
   * @param null $id
   * @param null $appId
   * @param null $method
   * @param null $identifier
   * @param null $meta
   * @param null $ttl
   */
  public function __construct($id=NULL, $appId=NULL, $method=NULL, $identifier=NULL, $meta=NULL, $ttl=NULL)
  {
    $this->id = $id;
    $this->appId = $appId;
    $this->method = $method;
    $this->identifier = $identifier;
    $this->meta = $meta;
    $this->ttl = $ttl;
  }

  /**
   * @return int id
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param $val
   */
  public function setId($val)
  {
    $this->id = $val;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appId;
  }

  /**
   * @param $val
   */
  public function setAppId($val)
  {
    $this->appId = $val;
  }

  /**
   * @return string method
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @param $val
   */
  public function setMethod($val)
  {
    $this->method = $val;
  }

  /**
   * @return string identifier
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }

  /**
   * @param $val
   */
  public function setIdentifier($val)
  {
    $this->identifier = $val;
  }

  /**
   * @return string meta
   */
  public function getMeta()
  {
    return $this->meta;
  }

  /**
   * @param $val
   */
  public function setMeta($val)
  {
    $this->meta = $val;
  }

  /**
   * @return int ttl
   */
  public function getTtl()
  {
    return $this->ttl;
  }

  /**
   * @param $val
   */
  public function setTtl($val)
  {
    $this->ttl = $val;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->id, 'id');
    Core\Debug::variable($this->appId, 'appid');
    Core\Debug::variable($this->method, 'method');
    Core\Debug::variable($this->identifier, 'identifier');
    Core\Debug::variable($this->meta, 'meta');
    Core\Debug::variable($this->ttl, 'ttl');
  }
}
