<?php

/**
 * Class Cache.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

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
     * Cache active status.
     *
     * @var boolean Active status of cache.
     */
    private bool $active;

    /**
     * Cache object.
     *
     * @var mixed Cache object.
     */
    private $cacheObj;

    /**
     * Constructor.
     *
     * @param array $config Api cache config.
     * @param MonologWrapper $logger Logging object.
     *
     * @throws ApiException
     */
    public function __construct(array $config, MonologWrapper $logger)
    {
        $this->logger = $logger;
        $this->active = $config['active'];
        $this->logger->debug('api', 'Cache active: ' . print_r($this->active, true));

        if ($this->active) {
            switch ($config['type']) {
                case 'memcached':
                    $this->cacheObj = new CacheMemcached($config['servers'], $logger);
                    break;
                case 'redis':
                    $this->cacheObj = new CacheRedis($config['servers'], $logger);
                    break;
                default:
                    throw new ApiException(
                        'Invalid cache type: ' . $config['type'],
                        8,
                        'oops',
                        500
                    );
            }
        }
    }

    /**
     * Store a value in the cache
     *
     * @param string $key Cache key.
     * @param mixed $val Value to store.
     * @param integer $ttl Cache TTL. Time to live (in seconds). 0|-1 = no cache.
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function set(string $key, $val, int $ttl): bool
    {
        if (!$this->active || $ttl < 1) {
            $this->logger->debug('api', 'not caching');
            return false;
        }

        $this->logger->debug('api', 'setting in cache (key, ttl): ' . $key . ', ' . $ttl);
        return $this->cacheObj->set($key, $val, $ttl);
    }

    /**
     * Fetch a value from the cache.
     *
     * @param string $key Get value for a cache key.
     *
     * @return mixed results on success, false on failure
     *
     * @throws ApiException
     */
    public function get(string $key)
    {
        if (!$this->active) {
            return null;
        }

        $this->logger->debug('api', "fetching from cache key: $key");
        return $this->cacheObj->get($key);
    }

    /**
     * Clear cache.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function clear(): bool
    {
        $this->logger->notice('api', 'clearing cache');
        if (!$this->active) {
            $this->logger->warning('api', 'could not clear cache - inactive');
            return false;
        }

        return $this->cacheObj->clear();
    }

  /**
   * Return the status of cache (active or inactive).
   *
   * @return boolean
   */
    public function active(): bool
    {
        return $this->active;
    }
}
