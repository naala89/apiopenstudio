<?php
/**
 * Class Account.
 *
 * @package    Gaterdata
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class Account.
 *
 * DB class for for storing role account data.
 */
class Account
{
    /**
     * Account ID.
     *
     * @var integer Account ID.
     */
    protected $accid;

    /**
     * Account name.
     *
     * @var string Account name.
     */
    protected $name;

    /**
     * Account constructor.
     *
     * @param integer $accid Account ID.
     * @param string $name Account name.
     */
    public function __construct(int $accid = null, string $name = null)
    {
        $this->accid = $accid;
        $this->name = $name;
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
     * Get the account name.
     *
     * @return string Account name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the account name.
     *
     * @param string $name Account name.
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Account.
     */
    public function dump()
    {
        return [
            'accid' => $this->accid,
            'name' => $this->name,
        ];
    }
}
