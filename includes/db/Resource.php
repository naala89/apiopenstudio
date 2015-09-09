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
   * @param $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appId;
  }

  /**
   * @param $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }

  /**
   * @return string method
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @param $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }

  /**
   * @return string identifier
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }

  /**
   * @param $identifier
   */
  public function setIdentifier($identifier)
  {
    $this->identifier = $identifier;
  }

  /**
   * @return string meta
   */
  public function getMeta()
  {
    return $this->meta;
  }

  /**
   * @param $meta
   */
  public function setMeta($meta)
  {
    $this->meta = $meta;
  }

  /**
   * @return int ttl
   */
  public function getTtl()
  {
    return $this->ttl;
  }

  /**
   * @param $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
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
