<?php

/**
 * Class Role.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

/**
 * Class Role.
 *
 * DB class for for storing role row data.
 */
class Role
{
    /**
     * Role ID.
     *
     * @var integer Role ID.
     */
    protected $rid;

    /**
     * Role name.
     *
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
