<?php
/**
 * Class VarStore.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class VarStore.
 *
 * DB class for for storing var row data.
 */
class VarStore
{
    /**
     * @var integer Var store ID.
     */
    protected $vid;

    /**
     * @var integer Application ID.
     */
    protected $appid;

    /**
     * @var string Var key.
     */
    protected $key;

    /**
     * @var mixed Var value.
     */
    protected $val;

    /**
     * @param integer $vid The var ID.
     * @param integer $appid The var application ID.
     * @param string $key The var key.
     * @param mixed $val The var value.
     */
    public function __construct(int $vid = null, int $appid = null, string $key = null, $val = null)
    {
        $this->vid = $vid;
        $this->appid = $appid;
        $this->key = $key;
        $this->val = $val;
    }

    /**
     * Get the var ID.
     *
     * @return integer The var ID.
     */
    public function getVid()
    {
        return $this->vid;
    }

    /**
     * Set the var ID.
     *
     * @param integer $vid The var ID.
     *
     * @return void
     */
    public function setVid(int $vid)
    {
        $this->vid = $vid;
    }

    /**
     * Get the var application ID.
     *
     * @return integer The var application ID.
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * Set the var application ID.
     *
     * @param integer $appid The avr application ID.
     *
     * @return void
     */
    public function setAppid(int $appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the var key.
     *
     * @return string
     *   The var key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the var key.
     *
     * @param string $key The var key.
     *
     * @return void
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get the var value.
     *
     * @return string
     *   The var value.
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * Set the var value.
     *
     * @param mixed $val The var value.
     *
     * @return void
     */
    public function setVal($val)
    {
        $this->val = $val;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Associative array of var attributes.
     */
    public function dump()
    {
        return [
            'vid' => $this->vid,
            'appid' => $this->appid,
            'key' => $this->key,
            'val' => $this->val,
        ];
    }
}
