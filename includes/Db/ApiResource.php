<?php

namespace Datagator\Db;

/**
 * Class ApiResource.
 *
 * @package Datagator\Db
 */
class ApiResource {

  protected $resid;
  protected $appid;
  protected $name;
  protected $description;
  protected $method;
  protected $identifier;
  protected $meta;
  protected $ttl;

  /**
   * Resource constructor.
   *
   * @param int $resid
   *   The resource ID.
   * @param int $appid
   *   The application ID.
   * @param string $name
   *   The resource name.
   * @param string $description
   *   The resource description.
   * @param string $method
   *   The resource method.
   * @param string $identifier
   *   The resource identifier.
   * @param string $meta
   *   The resource metadata.
   * @param string $ttl
   *   The resource TTL.
   */
  public function __construct($resid = NULL, $appid = NULL, $name = NULL, $description = NULL, $method = NULL, $identifier = NULL, $meta = NULL, $ttl = NULL) {
    $this->resid = $resid;
    $this->appid = $appid;
    $this->name = $name;
    $this->description = $description;
    $this->method = $method;
    $this->identifier = $identifier;
    $this->meta = $meta;
    $this->ttl = $ttl;
  }

  /**
   * Get the resource ID.
   *
   * @return int
   *   The resource ID
   */
  public function getResid() {
    return $this->resid;
  }

  /**
   * Set the resource ID.
   *
   * @param int $resid
   *   The resource ID.
   */
  public function setResid($resid) {
    $this->resid = $resid;
  }

  /**
   * Get the application ID.
   *
   * @return int
   *   The application ID.
   */
  public function getAppId() {
    return $this->appid;
  }

  /**
   * Set the resource application ID.
   *
   * @param int $appid
   *   The application ID.
   */
  public function setAppId($appid) {
    $this->appid = $appid;
  }

  /**
   * Get the resource nanme.
   *
   * @return string
   *   The resource name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the resource name.
   *
   * @param string $name
   *   The resource name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get the resource description.
   *
   * @return string
   *   The resource description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set the resource description.
   *
   * @param string $description
   *   The resource description.
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Get the resource method.
   *
   * @return string
   *   Resource method.
   */
  public function getMethod() {
    return $this->method;
  }

  /**
   * Set the resource method.
   *
   * @param string $method
   *   The resource method.
   */
  public function setMethod($method) {
    $this->method = $method;
  }

  /**
   * Get the resource identifier.
   *
   * @return string
   *   The  resource identifier.
   */
  public function getIdentifier() {
    return $this->identifier;
  }

  /**
   * Set the resource identifier.
   *
   * @param string $identifier
   *   Resource identifier.
   */
  public function setIdentifier($identifier) {
    $this->identifier = $identifier;
  }

  /**
   * Get the json encoded resource metadata.
   *
   * @return string
   *   Json encoded resource metadata.
   */
  public function getMeta() {
    return $this->meta;
  }

  /**
   * Set the json encoded resource metadata.
   *
   * @param string $meta
   *   The json encoded resource metadata.
   */
  public function setMeta($meta) {
    $this->meta = $meta;
  }

  /**
   * Get the resource TTL.
   *
   * @return int
   *   Time to live.
   */
  public function getTtl() {
    return $this->ttl;
  }

  /**
   * Set the TTL.
   *
   * @param int $ttl
   *   Time to live.
   */
  public function setTtl($ttl) {
    $this->ttl = $ttl;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   Api resource.
   */
  public function dump() {
    return array(
      'resid' => $this->resid,
      'appid' => $this->appid,
      'method' => $this->method,
      'identifier' => $this->identifier,
      'ttl' => $this->ttl,
    );
  }

}
