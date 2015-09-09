<?php

namespace Datagator\Core;

/**
 * Class Cache
 *
 * allow storing values in the cache.
 */
class Cache
{
  private $caches = array('memcache', 'apc');
  private $cacheObj;
  private $cacheType;
  private $cacheActive;

  public $host = '127.0.0.1';
  public $port = '11211';

  /**
   * Constructor.
   *
   * @param bool $cache
   *    False means do not cache
   *    True means select first available caching system
   *    String means select the specified caching system
   * @return bool
   */
  public function Cache($cache=TRUE)
  {
    Debug::variable($cache, 'cache setup request', 4);
    $this->cacheActive = FALSE;

    if ($cache === TRUE || $cache == 1) {
      $caches = $this->caches;
      while (!$this->cacheActive && $cache = array_shift($caches)) {
        $func = 'setup' . ucfirst($cache);
        Debug::variable($func, 'looking for function', 4);
        if (method_exists($this, $func)) {
          $this->$func();
        }
      }
    } elseif ($cache === FALSE || $cache == 0) {
      Debug::message('Cache is off', 4);
      return FALSE;
    } else {
      $func = 'setup' . ucfirst(trim($cache));
      Debug::variable($func, 'looking for function', 4);
      if (method_exists($this, $func)) {
        $this->$func();
      } else {
        Debug::variable($func, 'function not defined');
      }
    }

    Debug::variable($this->cacheType, 'cache type enbled', 2);
    Debug::variable($this->cacheActive, 'cache status', 2);

    return $this->cacheActive;
  }

  /**
   * Store a value in the cache
   *
   * @param $key
   * @param $val
   * @param $ttl
   *    time to live (in seconds)
   *    0|-1 = no cache
   * @return bool
   *    success
   */
  public function set($key, $val, $ttl)
  {
    if (!$this->cacheActive || $ttl < 1) {
      Debug::message('not caching', 4);
      return FALSE;
    }
    Debug::variable($key, 'setting in cache (key)', 4);
    Debug::variable($ttl, 'setting in cache (ttl)', 4);

    $func = 'set' . ucfirst($this->cacheType);
    $success = FALSE;
    if (method_exists($this, $func)) {
      $success = $this->$func($key, $val, $ttl);
    }

    return $success;
  }

  /**
   * Fetch a value from the cache
   *
   * @param $key
   * @return mixed
   *    results on success, false on failure
   */
  public function get($key)
  {
    if (!$this->cacheActive) {
      return NULL;
    }
    $func = 'get' . ucfirst($this->cacheType);
    if (method_exists($this, $func)) {
      return $this->$func($key);
    }
    return NULL;
  }

  public function clear()
  {
    Debug::message('clearing cache');
    if (!$this->cacheActive) {
      Debug::message('could not clear cache - inactive');
      return FALSE;
    }
    $func = 'clear' . ucfirst($this->cacheType);
    if (method_exists($this, $func)) {
      return $this->$func();
    }
    return NULL;
  }

  /**
   * Return the status of cache (active or inactive).
   *
   * @return mixed
   */
  public function cacheActive()
  {
    return $this->cacheActive;
  }

  /**
   * Setup MemCache, based on params passed in setup()
   *
   * @return bool
   */
  private function setupMemcache()
  {
    $this->cacheActive = FALSE;

    if (class_exists('memcache')) {
      Debug::message('memcache available', 4);
      $this->$cacheObj = new Memcache;
      if ($this->cacheActive = $this->cache->connect($this->host, $this->port)) {
        $this->cacheType = 'memcache';
        Debug::message('memCache enabled', 4);
      } else {
        Debug::message('Could not connect to Memcache', 4);
      }
    } else {
      Debug::message('memcache not available', 2);
    }

    return $this->cacheActive;
  }

  /**
   * Store a value in MemCache
   *
   * @param $key
   * @param $val
   * @param $ttl
   * @return bool
   */
  private function setMemcache($key, $val, $ttl)
  {
    return $this->cacheObj->set($key, $val, $ttl);
  }

  /**
   * Fetch a value from MemCache
   *
   * @param $key
   * @return mixed
   */
  private function getMemcache($key)
  {
    return $this->cacheObj->get($key);
  }

  /**
   * Clear the MmeCache cache.
   *
   * @return bool
   */
  private function clearMemcache()
  {
    return $this->cacheObj->flush();
  }

  /**
   * Setup APC, based on params passed in setup()
   *
   * @return mixed
   */
  private function setupApc()
  {
    $this->cacheAvailable = FALSE;

    if ($this->cacheAvailable = extension_loaded('apc')) {
      Debug::message('apc available', 4);
      $this->cacheType = 'apc';
      Debug::message('apc enabled', 4);
      $this->cacheActive = TRUE;
    } else {
      Debug::message('apc not available', 2);
    }

    return $this->cacheActive;
  }

  /**
   * Store a value in APC
   *
   * @param $key
   * @param $val
   * @param $ttl
   * @return bool
   */
  private function setApc($key, $val, $ttl)
  {
    return apc_store($key, $val, $ttl);
  }

  /**
   * Fetch a value from APC
   *
   * @param $key
   * @return mixed
   */
  private function getApc($key)
  {
    return apc_fetch($key);
  }

  /**
   * Clear the APC cache.
   *
   * @return bool
   */
  private function clearApc()
  {
    return apc_clear_cache();
  }
}
