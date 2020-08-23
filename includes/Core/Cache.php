<?php

namespace Gaterdata\Core;

/**
 * Class Cache
 *
 * allow storing values in the cache.
 */
class Cache
{

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    private $caches = ['memcache', 'apc'];
    private $cacheObj;
    private $cacheType;
    private $cacheActive;

    public $host = '127.0.0.1';
    public $port = '11211';

  /**
   * Constructor.
   *
   * @param Monolog\Logger $logger
   * @param bool $cache
   *    False means do not cache
   *    True means select first available caching system
   *    String means select the specified caching system
   * @return bool
   */
    public function __construct($logger, $cache = true)
    {
        $this->logger = $logger;
        $this->logger->debug('cache setup request: ' . print_r($cache, false));
        $this->cacheActive = false;

        if ($cache === true || $cache == 1) {
            $caches = $this->caches;
            while (!$this->cacheActive && $cache = array_shift($caches)) {
                $func = 'setup' . ucfirst($cache);
                $this->logger->info('looking for function: ' . $func);
                if (method_exists($this, $func)) {
                    $this->$func();
                }
            }
        } elseif ($cache === false || $cache == 0) {
            $this->logger->info('Cache is off');
            return false;
        } else {
            $func = 'setup' . ucfirst(trim($cache));
            $this->logger->info('looking for function: ' . $func);
            if (method_exists($this, $func)) {
                $this->$func();
            } else {
                $this->logger->info('function not defined');
            }
        }

        $this->logger->info('cache type enbled: ' . $this->cacheType);
        $this->logger->info('cache status: ' . $this->cacheActive);

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
            $this->logger->info('not caching');
            return false;
        }
        $this->logger->debug('setting in cache (key): ' . $key);
        $this->logger->debug('setting in cache (ttl): ' . $ttl);

        $func = 'set' . ucfirst($this->cacheType);
        $success = false;
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
            return null;
        }
        $func = 'get' . ucfirst($this->cacheType);
        if (method_exists($this, $func)) {
            return $this->$func($key);
        }
        return null;
    }

    public function clear()
    {
        $this->logger->notice('clearing cache');
        if (!$this->cacheActive) {
            $this->logger->warning('could not clear cache - inactive');
            return false;
        }
        $func = 'clear' . ucfirst($this->cacheType);
        if (method_exists($this, $func)) {
            return $this->$func();
        }
        return null;
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
        $this->cacheActive = false;

        if (class_exists('memcache')) {
            $this->logger->info('memcache available');
            $this->cacheObj = new Memcached();
            if ($this->cacheActive = $this->cacheObj->addServer($this->host, $this->port)) {
                $this->cacheType = 'memcache';
                $this->logger->info('memcache enabled');
            } else {
                $this->logger->error('Could not connect to Memcache');
            }
        } else {
            $this->logger->info('memcache not available');
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
        $this->cacheAvailable = false;

        if ($this->cacheAvailable = extension_loaded('apc')) {
            $this->logger->info('apc available');
            $this->cacheType = 'apc';
            $this->logger->info('apc enabled');
            $this->cacheActive = true;
        } else {
            $this->logger->info('apc not available');
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
