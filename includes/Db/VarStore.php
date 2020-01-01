<?php

namespace Gaterdata\Db;

/**
 * Class VarStore.
 *
 * @package Gaterdata\Db
 */
class VarStore
{
    /**
     * @var int Var store ID.
     */
    protected $vid;

    /**
     * @var int Application ID.
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
     * @param int $vid
     *   The var ID.
     * @param int $appid
     *   The var application ID.
     * @param string $key
     *   The var key.
     * @param mixed $val
     *   The var value.
     */
    public function __construct($vid = null, $appid = null, $key = null, $val = null)
    {
        $this->vid = $vid;
        $this->appid = $appid;
        $this->key = $key;
        $this->val = $val;
    }

    /**
     * Get the var ID.
     *
     * @return int
     *   The var ID.
     */
    public function getVid()
    {
        return $this->vid;
    }

    /**
     * Set the var ID.
     *
     * @param int $vid
     *   The var ID.
     */
    public function setVid($vid)
    {
        $this->vid = $vid;
    }

    /**
     * Get the var application ID.
     *
     * @return int
     *   The var application ID.
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * Set the var application ID.
     *
     * @param int $appid
     *   The avr application ID.
     */
    public function setAppid($appid)
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
     * @param string $key
     *   The var key.
     */
    public function setKey($key)
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
     * @param mixed $val
     *   The var value.
     */
    public function setVal($val)
    {
        $this->val = $val;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array
     *   Associative array of var attributes.
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
