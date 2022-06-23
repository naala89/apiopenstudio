<?php

/**
 * Class CacheMemcached.
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

use Memcached;

/**
 * Class CacheMemcached
 *
 * Interact with Memcache.
 */
class CacheMemcached
{
    /**
     * @var Memcached
     */
    protected Memcached $memcached;

    /**
     * @var MonologWrapper
     */
    protected MonologWrapper $logger;

    /**
     * CacheMemcached constructor.
     *
     * @param array $servers
     * @param MonologWrapper $logger
     *
     * @throws ApiException
     */
    public function __construct(array $servers, MonologWrapper $logger)
    {
        $this->memcached = new Memcached();
        $this->logger = $logger;
        $this->init($servers);
    }

    /**
     * Initiate the memcached servers.
     *
     * @param array $servers
     * @return bool
     * @throws ApiException
     */
    public function init(array $servers): bool
    {
        if (Utilities::isAssoc($servers)) {
            return $this->addServer($servers['host'], $servers['port'], $servers['weight']);
        }

        $result = true;
        foreach ($servers as $server) {
            $added = $this->addServer($server['host'], $server['port'], $server['weight']);
            $result = !$added ? false : $result;
        }
        return $result;
    }

    /**
     * Add a sinle or array of servers to memcached.
     *
     * @param string $host
     * @param int $port
     * @param int $weight
     *
     * @return bool
     *
     * @throws ApiException
     */
    protected function addServer(string $host = '127.0.0.1', int $port = 11211, int $weight = 1): bool
    {
        $servers = $this->memcached->getServerList();
        if (is_array($servers)) {
            foreach ($servers as $server) {
                if ($server['host'] == $host && $server['port'] == $port) {
                    $this->logger->error(
                        'api',
                        'Memcached instance already exists: ' . $server['host'] . ':' . $server['port']
                    );
                    return false;
                }
            }
        }
        $this->memcached->addServer($host, $port, $weight);
        return true;
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
    public function set(string $key, $val, int $ttl): bool
    {
        return $this->memcached->set($key, $val, $ttl);
    }

    /**
     * Fetch a value from MemCache
     *
     * @param string $key Memcache cache key.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->memcached->get($key);
    }

    /**
     * Clear the MmeCache cache.
     *
     * @return boolean
     */
    public function clear(): bool
    {
        return $this->memcached->flush();
    }
}
