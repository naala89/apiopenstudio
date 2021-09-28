<?php

/**
 * Class UserRole.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

/**
 * Class UserRole.
 *
 * DB class for for storing user_role row data.
 */
class UserRole
{
    /**
     * User role ID.
     *
     * @var integer|null User role ID.
     */
    protected ?int $urid;

    /**
     * User role account ID.
     *
     * @var integer|null Account ID.
     */
    protected ?int $accid;

    /**
     * User role application ID.
     *
     * @var integer|null Application ID.
     */
    protected ?int $appid;

    /**
     * User role user ID.
     *
     * @var integer|null User ID.
     */
    protected ?int $uid;

    /**
     * User role role ID.
     *
     * @var integer|null Role ID.
     */
    protected ?int $rid;

    /**
     * UserRole constructor.
     *
     * @param int|null $urid User role ID.
     * @param int|null $accid Account ID.
     * @param int|null $appid Application ID.
     * @param int|null $uid User ID.
     * @param int|null $rid The role ID.
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
    public function getUrid(): ?int
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
    public function getAccid(): ?int
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
    public function getAppid(): ?int
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
    public function getUid(): ?int
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
    public function getRid(): ?int
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
    public function dump(): array
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
