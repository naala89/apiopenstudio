<?php

namespace Gaterdata\Core;

use Spyc;

class Config
{

  /**
   * @var array
   */
    private $conf;
  
    public function __construct()
    {
        $this->conf = Spyc::YAMLLoad(dirname(dirname(__DIR__)) . '/settings.yml');
    }

  /**
   * Get a config value.
   *
   * @param array|string $key
   *  The key of the config value.
   *
   * @return string|NULL
   *  The value of the config key or NULL.
   *
   * @throws ApiException
   */
    public function __get($key)
    {
        if (is_string($key)) {
            if (array_key_exists($key, $this->conf)) {
                return $this->conf[$key];
            }
            throw new ApiException("Invalid configuration option: $key");
        }

        if (is_array($key)) {
            $val = $this->conf;
            while ($index = array_shift($key)) {
                if (!array_key_exists($index, $val)) {
                    throw new ApiException("Invalid configuration option: $index");
                }
                $val = $val[$index];
            }
            return $val;
        }

        throw new ApiException('Invalid configuration key');
    }

    /**
     * Fetch all config values.
     *
     * @return array|false
     *   Config file values.
     */
    public function all()
    {
        return $this->conf;
    }
}
