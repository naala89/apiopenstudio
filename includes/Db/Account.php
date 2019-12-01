<?php

namespace Gaterdata\Db;

/**
 * Class Account.
 *
 * @package Gaterdata\Db
 */
class Account
{
    /**
     * @var int Account ID.
     */
    protected $accid;
    /**
     * @var string Account name.
     */
    protected $name;

    /**
     * Account constructor.
     *
     * @param int $accid
     *   Account ID.
     * @param string $name
     *   Account name.
     */
    public function __construct($accid = null, $name = null)
    {
        $this->accid = $accid;
        $this->name = $name;
    }

    /**
     * Get the account ID.
     *
     * @return int
     *   Account ID.
     */
    public function getAccid()
    {
        return $this->accid;
    }

    /**
     * Set the account ID.
     *
     * @param int $accid
     *   Account ID.
     */
    public function setAccid($accid)
    {
        $this->accid = $accid;
    }

    /**
     * Get the account name.
     *
     * @return string
     *   Account name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the account name.
     *
     * @param string $name
     *   Account name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array
     *   Account.
     */
    public function dump()
    {
        return [
        'accid' => $this->accid,
        'name' => $this->name,
        ];
    }
}
