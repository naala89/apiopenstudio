<?php

/**
 * Class Role.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

/**
 * Class Role.
 *
 * DB class for storing role row data.
 */
class Role
{
    /**
     * Role ID.
     *
     * @var integer|null Role ID.
     */
    protected ?int $rid;

    /**
     * Role name.
     *
     * @var string|null Role name.
     */
    protected ?string $name;

    /**
     * Role constructor.
     *
     * @param int|null $rid Role ID.
     * @param string|null $name Role name.
     */
    public function __construct(int $rid = null, string $name = null)
    {
        $this->rid = $rid;
        $this->name = $name;
    }

    /**
     * Get the role ID.
     *
     * @return int Role ID.
     */
    public function getRid(): ?int
    {
        return $this->rid;
    }

    /**
     * Set the role ID.
     *
     * @param int $rid Role ID.
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
     * @return string Name
     */
    public function getName(): ?string
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
    public function dump(): array
    {
        return [
            'rid' => $this->rid,
            'name' => $this->name,
        ];
    }
}
