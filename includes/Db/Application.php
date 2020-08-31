<?php
/**
 * Class Account.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class Account.
 *
 * DB class for for storing account row data.
 */
class Application
{
    /**
     * @var integer Application ID.
     */
    protected $appid;

    /**
     * @var integer Account ID.
     */
    protected $accid;

    /**
     * @var string Application name.
     */
    protected $name;

    /**
     * Application constructor.
     *
     * @param integer $appid Application ID.
     * @param integer $accid Account ID.
     * @param string $name Application name.
     */
    public function __construct(int $appid = null, int $accid = null, string $name = null)
    {
        $this->appid = $appid;
        $this->accid = $accid;
        $this->name = $name;
    }

    /**
     * Get application IOD.
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
     * Get the application name.
     *
     * @return string Application name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the application name.
     *
     * @param string $name Application name.
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
     * @return array Application.
     */
    public function dump()
    {
        return [
            'appid' => $this->appid,
            'accid' => $this->accid,
            'name' => $this->name,
        ];
    }
}
