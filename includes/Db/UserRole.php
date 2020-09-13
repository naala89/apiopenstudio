<?php
/**
 * Class UserRole.
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
 * Class UserRole.
 *
 * DB class for for storing user_role row data.
 */
class UserRole
{
    /**
     * @var integer User role ID.
     */
    protected $urid;

    /**
     * @var integer Account ID.
     */
    protected $accid;

    /**
     * @var integer Application ID.
     */
    protected $appid;

    /**
     * @var integer User ID.
     */
    protected $uid;

    /**
     * @var integer Role ID.
     */
    protected $rid;

    /**
     * UserRole constructor.
     *
     * @param integer $urid User role ID.
     * @param integer $accid Account ID.
     * @param integer $appid Application ID.
     * @param integer $uid User ID.
     * @param integer $rid The role ID.
     */
    public function __construct(
        int $urid = null,
        int $accid = null,
        int $appid = null,
        int $uid = null,
        int $rid = null
    ) {
        $this->urid = $urid;
        $this->accid = $accid;
        $this->appid = $appid;
        $this->uid = $uid;
        $this->rid = $rid;
    }

    /**
     * Get the user role ID.
     *
     * @return integer user role ID.
     */
    public function getUrid()
    {
        return $this->urid;
    }

    /**
     * Set the user role ID.
     *
     * @param integer $urid User role ID.
     *
     * @return void
     */
    public function setUrid(int $urid)
    {
        $this->urid = $urid;
    }

    /**
     * Get the account ID.
     *
     * @return integer Account ID.
     */
    public function getAccid()
    {
        return $this->accid;
    }

    /**
     * Set the account ID.
     *
     * @param integer $accid Account ID.
     *
     * @return void
     */
    public function setAccid(int $accid)
    {
        $this->accid = $accid;
    }

    /**
     * Get the application ID.
     *
     * @return integer Application ID.
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * Set the application ID.
     *
     * @param integer $appid Application ID.
     *
     * @return void
     */
    public function setAppid(int $appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the user ID.
     *
     * @return integer User ID.
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set the user ID.
     *
     * @param integer $uid User ID.
     *
     * @return void
     */
    public function setUid(int $uid)
    {
        $this->uid = $uid;
    }

    /**
     * Get the role ID.
     *
     * @return integer The role ID.
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set the role ID.
     *
     * @param integer $rid The role ID.
     *
     * @return void
     */
    public function setRid(int $rid)
    {
        $this->rid = $rid;
    }

    /**
     * Return the user account role as an associative array.
     *
     * @return array Associative array.
     */
    public function dump()
    {
        return [
            'urid' => $this->urid,
            'accid' => $this->accid,
            'appid' => $this->appid,
            'uid' => $this->uid,
            'rid' => $this->rid,
        ];
    }
}
