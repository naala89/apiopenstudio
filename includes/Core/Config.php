<?php

/**
 * Class Config.
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

use Spyc;

/**
 * Class Config
 *
 * ApiOpenStudio config.
 */
class Config
{
    /**
     * Internal config array.
     *
     * @var array
     */
    private array $conf;

    /**
     * Config constructor.
     *
     * @param string $settingsFile
     *   Optional path to the settings file.
     */
    public function __construct(string $settingsFile = '')
    {
        $settingsFile = empty($settingsFile) ? dirname(__DIR__, 2) . '/settings.yml' : $settingsFile;
        $this->conf = Spyc::YAMLLoad($settingsFile);
    }

    /**
     * Get a config value.
     *
     * @param array|string $key The key of the config value.
     *
     * @return mixed The value of the config key or NULL.
     *
     * @throws ApiException Invalid config defined.
     */
    public function __get($key)
    {
        if (is_string($key)) {
            if (array_key_exists($key, $this->conf)) {
                return $this->conf[$key];
            }
            throw new ApiException("Invalid configuration option: $key", 8, -1, 500);
        }

        if (is_array($key)) {
            $val = $this->conf;
            while ($index = array_shift($key)) {
                if (!array_key_exists($index, $val)) {
                    print_r($key, true);
                    print_r($index, true);
                    print_r($val, true);
                    throw new ApiException("Invalid configuration option: $index", 8, -1, 500);
                }
                $val = $val[$index];
            }
            return $val;
        }

        throw new ApiException('Invalid configuration key', 8, -1, 500);
    }

    /**
     * Fetch all config values.
     *
     * @return array Config file values.
     */
    public function all(): array
    {
        return $this->conf;
    }
}
