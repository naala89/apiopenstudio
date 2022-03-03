<?php

/**
 * Class RoleMapper.
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

use ApiOpenStudio\Core\ApiException;

/**
 * Class RoleMapper.
 *
 * Mapper class for DB calls used for the role table.
 */
class RoleMapper extends Mapper
{
    /**
     * Save a Role object into the DB.
     *
     * @param Role $role Role object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(Role $role): bool
    {
        if ($role->getRid() == null) {
            $sql = 'INSERT INTO role (name) VALUES (?)';
            $bindParams = [
            $role->getName(),
            ];
        } else {
            $sql = 'UPDATE role SET name = ? WHERE rid = ?';
            $bindParams = [
            $role->getName(),
            $role->getRid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete a Role.
     *
     * @param Role $role Role object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(Role $role): bool
    {
        $sql = 'DELETE FROM role WHERE rid = ?';
        $bindParams = [$role->getRid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all roles.
     *
     * @param array $params Filter parameters.
     *
     * @return array Array of role objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = []): array
    {
        $sql = 'SELECT * FROM role';
        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Find a role by its ID.
     *
     * @param integer $rid Role ID.
     *
     * @return Role Role object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByRid(int $rid): Role
    {
        $sql = 'SELECT * FROM role WHERE rid = ?';
        $bindParams = [$rid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a role by its name.
     *
     * @param string $name Role name.
     *
     * @return Role Role object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByName(string $name): Role
    {
        $sql = 'SELECT * FROM role WHERE name = ?';
        $bindParams = [$name];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a roles a user has assigned.
     *
     * @param integer $uid User ID.
     *
     * @return array Array of role objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid): array
    {
        $sql = 'SELECT DISTINCT r.* FROM role r INNER JOIN user_role ur ON ur.rid = r.rid WHERE ur.uid = ?';
        $bindParams = [$uid];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a DB row to the internal attributes.
     *
     * @param array $row DB row.
     *
     * @return Role
     *   Role object.
     */
    protected function mapArray(array $row): Role
    {
        $role = new Role();

        $role->setRid(!empty($row['rid']) ? $row['rid'] : 0);
        $role->setName(!empty($row['name']) ? $row['name'] : '');

        return $role;
    }
}
