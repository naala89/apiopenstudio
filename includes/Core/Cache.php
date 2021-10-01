<?php

/**
 * Class Cache.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use Memcached;

/**
 * Class Cache
 *
 * Allow storing values in the cache.
 */
class Cache
{
    /**
     * Logging class.
     *
     * @var MonologWrapper
     */
    private MonologWrapper $logger;

    /**
     * Current supported caches.
     *
     * @var string[] Liust of supported caches.
     */
    private array $caches = ['memcache', 'apc'];

    /**
     * Cache object.
     *
     * @var mixed Cache object.
     */
    private $cacheObj;

    /**
     * Cache type.
     *
     * @var string Caching to use.
     */
    private string $cacheType;

    /**
     * Cache active/inactive.
     *
     * @var boolean Active status of cache.
     */
    private bool $cacheActive;

    /**
     * Cache host.
     *
     * @var string Cache host.
     */
    public $host;

    /**
     * Cache port.
     *
     * @var string Cache port.
     */
    public $port;

    /**
     * Constructor.
     *
     * @param array $config Config array.
     * @param MonologWrapper $logger Logging object.
     * @param boolean $cache Set cache on or off.
     *
     *    False means do not cache.
     *    True means select first available caching system.
     *    String means select the specified caching system.
     *
     * @return boolean
     * @throws ApiException
     */
    public function __construct(array $config, MonologWrapper $logger, bool $cache = false)
    {
        $this->logger = $logger;
        $this->logger->info('api', 'Caching request with cache set to: ' . print_r($cache, true));
        $this->cacheActive = false;

        if ($cache === true || $cache == 1) {
            $this->host = $config['api']['cache_host'];
            $this->port = $config['api']['cache_port'];
            $caches = $this->caches;
            while (!$this->cacheActive && $cache = array_shift($caches)) {
                $func = 'setup' . ucfirst($cache);
                $this->logger->info('api', 'looking for function: ' . $func);
                if (method_exists($this, $func)) {
                    $this->$func();
                }
            }
        } elseif (!$cache) {
            $this->logger->info('api', 'Cache is off');
            return false;
        } else {
            $func = 'setup' . ucfirst(trim($cache));
            $this->logger->info('api', 'looking for function: ' . $func);
            if (method_exists($this, $func)) {
                $this->$func();
            } else {
                $this->logger->info('api', 'function not defined');
            }
        }

        $this->logger->info('api', 'cache type enbled: ' . $this->cacheType);
        $this->logger->info('api', 'cache status: ' . $this->cacheActive);

        return $this->cacheActive;
    }

    /**
     * Store a value in the cache
     *
     * @param string $key Cache key.
     * @param mixed $val Value to store.
     * @param integer $ttl Cache TTL. Time to live (in seconds). 0|-1 = no cache.
     *
     * @return boolean
     * @throws ApiException
     */
    public function set(string $key, $val, int $ttl): bool
    {
        if (!$this->cacheActive || $ttl < 1) {
            $this->logger->info('api', 'not caching');
            return false;
        }
        $this->logger->debug('api', 'setting in cache (key): ' . $key);
        $this->logger->debug('api', 'setting in cache (ttl): ' . $ttl);

        $func = 'set' . ucfirst($this->cacheType);
        $success = false;
        if (method_exists($this, $func)) {
            $success = $this->$func($key, $val, $ttl);
        }

        return $success;
    }

  /**
   * Fetch a value from the cache.
   *
   * @param string $key Get value for a cache key.
   *
   * @return mixed results on success, false on failure
   */
    public function get(string $key)
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

    /**
     * Clear cache.
     *
     * @return false|null
     * @throws ApiException
     */
    public function clear(): ?bool
    {
        $this->logger->notice('api', 'clearing cache');
        if (!$this->cacheActive) {
            $this->logger->warning('api', 'could not clear cache - inactive');
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
   * @return boolean
   */
    public function cacheActive(): bool
    {
        return $this->cacheActive;
    }

    /**
     * Setup MemCache, based on params passed in setup()
     *
     * @return boolean
     * @throws ApiException
     */
    private function setupMemcache(): bool
    {
        $this->cacheActive = false;

        if (class_exists('memcache')) {
            $this->logger->info('api', 'memcache available');
            $this->cacheObj = new Memcached();
            if ($this->cacheActive = $this->cacheObj->addServer($this->host, $this->port)) {
                $this->cacheType = 'memcache';
                $this->logger->info('api', 'memcache enabled');
            } else {
                $this->logger->error('api', 'Could not connect to Memcache');
            }
        } else {
            $this->logger->info('api', 'memcache not available');
        }

        return $this->cacheActive;
    }

  /**
   * Store a value in MemCache
   *
   * @param string $key Memcache cache key.
   * @param mixed $val Value to store in Memcache.
   * @param integer $ttl Cache TTL.
   *
   * @return boolean
   */
    private function setMemcache(string $key, $val, int $ttl): bool
    {
        return $this->cacheObj->set($key, $val, $ttl);
    }

  /**
   * Fetch a value from MemCache
   *
   * @param string $key Memcache cache key.
   *
   * @return mixed
   */
    private function getMemcache(string $key)
    {
        return $this->cacheObj->get($key);
    }

  /**
   * Clear the MmeCache cache.
   *
   * @return boolean
   */
    private function clearMemcache(): bool
    {
        return $this->cacheObj->flush();
    }

    /**
     * Setup APC, based on params passed in setup()
     *
     * @return boolean
     * @throws ApiException
     */
    private function setupApc(): bool
    {
        $cacheAvailable = false;

        if ($cacheAvailable = extension_loaded('apc')) {
            $this->logger->info('api', 'apc available');
            $this->cacheType = 'apc';
            $this->logger->info('api', 'apc enabled');
            $this->cacheActive = true;
        } else {
            $this->logger->info('api', 'apc not available');
        }

        return $this->cacheActive;
    }

  /**
   * Store a value in APC
   *
   * @param string $key APC cache key.
   * @param mixed $val Value to cache.
   * @param integer $ttl TTL.
   *
   * @return boolean
   */
    private function setApc(string $key, $val, int $ttl): bool
    {
        return apc_store($key, $val, $ttl);
    }

  /**
   * Fetch a value from APC
   *
   * @param string $key APC cache key.
   *
   * @return mixed
   */
    private function getApc(string $key)
    {
        return apc_fetch($key);
    }

  /**
   * Clear the APC cache.
   *
   * @return boolean
   */
    private function clearApc(): bool
    {
        return apc_clear_cache();
    }
}
