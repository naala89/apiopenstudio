<?php

/**
 * Class CacheRedis.
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

use Redis;

/**
 * Class CacheRedis
 *
 * Interact with Redis.
 */
class CacheRedis
{
    /**
     * @var Redis
     */
    protected Redis $redis;

    /**
     * @var MonologWrapper
     */
    protected MonologWrapper $logger;

    /**
     * CacheMemcached constructor.
     *
     * @param array $servers
     * @param MonologWrapper $logger
     */
    public function __construct(array $servers, MonologWrapper $logger)
    {
        $this->logger = $logger;
        $this->redis = new Redis();
        $this->init($servers);
    }

    /**
     * Initiate the memcached servers.
     *
     * @param array $servers
     *
     * @return bool
     */
    public function init(array $servers): bool
    {
        if (Utilities::isAssoc($servers)) {
            return $this->addServer($servers['host'], $servers['port'], $servers['password']);
        }

        $result = true;
        foreach ($servers as $server) {
            $added = $this->addServer($server['host'], $server['port']);
            $result = !$added ? false : $result;
        }
        return $result;
    }

    /**
     * Add a single server to redis.
     *
     * @param string $host
     * @param int $port
     * @param string $password
     *
     * @return bool
     */
    protected function addServer(string $host = '127.0.0.1', int $port = 6379, string $password = ''): bool
    {
        $result = $this->redis->connect($host, $port);
        if ($result && !empty($password)) {
            $this->redis->auth($password);
        }
        return $result;
    }

    /**
     * Store a value in Redis
     *
     * @param string $key Redis cache key.
     * @param mixed $val Value to store in Redis.
     * @param integer $ttl Cache TTL.
     *
     * @return bool
     */
    public function set(string $key, $val, int $ttl): bool
    {
        return $this->redis->set($key, serialize($val), $ttl);
    }

    /**
     * Fetch a value from Redis. If the key does not exist, null is returned.
     *
     * @param string $key Redis cache key.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        $result = $this->redis->get($key);
        return $result === false ? null : unserialize($result);
    }

    /**
     * Clear the Redis cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->redis->flushAll();
    }
}
