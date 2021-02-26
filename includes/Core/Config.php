<?php

/**
 * Class Config.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
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
    private $conf;

    /**
     * Config constructor.
     */
    public function __construct($settingsFile = '')
    {
        $settingsFile = empty($settingsFile) ? dirname(dirname(__DIR__)) . '/settings.yml' : $settingsFile;
        $this->conf = Spyc::YAMLLoad($settingsFile);
    }

    /**
     * Get a config value.
     *
     * @param array|string $key The key of the config value.
     *
     * @return string|NULL The value of the config key or NULL.
     *
     * @throws ApiException Invalid config defined.
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
     * @return array|false Config file values.
     */
    public function all()
    {
        return $this->conf;
    }
}
