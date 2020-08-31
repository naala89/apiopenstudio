<?php
/**
 * Class Config.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

use Spyc;

/**
 * Class Config
 *
 * GaterData config.
 */
class Config
{
    /**
     * @var array
     */
    private $conf;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->conf = Spyc::YAMLLoad(dirname(dirname(__DIR__)) . '/settings.yml');
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
