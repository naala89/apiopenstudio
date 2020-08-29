<?php
/**
 * Class Role.
 *
 * @package Gaterdata\Db
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @author john89
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class Role.
 *
 * DB class for for storing role row data.
 */
class Role
{
    /**
     * @var integer Role ID.
     */
    protected $rid;

    /**
     * @var string Role name.
     */
    protected $name;

    /**
     * Role constructor.
     *
     * @param integer $rid Role ID.
     * @param string $name Role name.
     */
    public function __construct(int $rid = null, string $name = null)
    {
        $this->rid = $rid;
        $this->name = $name;
    }

    /**
     * Get the role ID.
     *
     * @return integer Role ID.
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set the role ID.
     *
     * @param integer $rid Role ID.
     *
     * @return void
     */
    public function setRid(int $rid)
    {
        $this->rid = $rid;
    }

    /**
     * Get the role name.
     *
     * @return integer Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the role name.
     *
     * @param string $name Role name.
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
     * @return array Role object.
     */
    public function dump()
    {
        return [
        'rid' => $this->rid,
        'name' => $this->name,
        ];
    }
}
