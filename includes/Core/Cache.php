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
        $this->logger->info('api', 'Cache active: ' . ($this->active ? 'true' : 'false'));

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
     * @param DataContainer $val Value to store.
     * @param integer $ttl Cache TTL. Time to live (in seconds). 0|-1 = no cache.
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function set(string $key, DataContainer $val, int $ttl): bool
    {
        if (!$this->active || $ttl < 1) {
            $this->logger->notice('api', "Not caching: $key");
            return false;
        }

        $this->logger->debug('api', "Setting in cache key (ttl): $key ($ttl)");
        return $this->cacheObj->set($key, $val->getData(), $ttl);
    }

    /**
     * Fetch a value from the cache.
     *
     * @param string $key Get value for a cache key.
     * @param bool $rawData Return raw data or DataContainer.
     *
     * @return ?DataContainer results on success, null if the key does not exist.
     *
     * @throws ApiException
     */
    public function get(string $key, bool $rawData = false): ?DataContainer
    {
        if (!$this->active) {
            $this->logger->notice('api', 'Not searching in cache - inactive');
            return null;
        }

        $this->logger->debug('api', "Looking for cache (key): $key");
        $result = $this->cacheObj->get($key);
        if (is_null($result)) {
            $this->logger->debug('api', "Cache not found (key): $key");
        } else {
            $this->logger->debug('api', "Cache found (key): $key");
            if (!$rawData) {
                $result = new DataContainer($result);
            }
        }

        return $result;
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
        $this->logger->notice('api', 'Clearing cache');
        if (!$this->active) {
            $this->logger->warning('api', 'Could not clear cache - inactive');
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

    /**
     * Returns the cache key for a resource.
     *
     * @param int $resId Resource ID.
     *
     * @return string
     */
    public function getResourceCacheKey(int $resId): string
    {
        return "resource_$resId";
    }

    /**
     * Returns the cache key for a processor.
     *
     * @param int $resId Resource ID.
     * @param string $processorId Processor ID.
     * @param string $inputsHash Hash of input values.
     * @return string
     */
    public function getProcessorCacheKey(int $resId, string $processorId, string $inputsHash): string
    {
        $processorId = preg_replace('/[^a-z\d]/i', '_', strtolower($processorId));
        return "processor_$resId" . '_' . $processorId . '_' . $inputsHash;
    }
}
