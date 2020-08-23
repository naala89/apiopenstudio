<?php

namespace Gaterdata\Core;

class Config
{

  /**
   * @var array
   */
    private $conf;
  
    public function __construct()
    {
        $this->conf = parse_ini_file(dirname(dirname(__DIR__)) . '/config/settings.ini', true, INI_SCANNER_TYPED);
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
