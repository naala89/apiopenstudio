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
 * Class UserRoleMapper.
 *
 * Mapper class for DB calls used for the user_role table.
 */
class UserRoleMapper extends Mapper
{
    /**
     * Save the user role.
     *
     * @param UserRole $userRole UserRole object.
     *
     * @return boolean Result of the save.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(UserRole $userRole): bool
    {
        if ($userRole->getUrid() == null) {
            $sql = 'INSERT INTO user_role (accid, appid, uid, rid) VALUES (?, ?, ?, ?)';
            $bindParams = [
                $userRole->getAccid(),
                $userRole->getAppid(),
                $userRole->getUid(),
                $userRole->getRid(),
            ];
        } else {
            $sql = 'UPDATE user_role SET accid = ?, appid = ?, uid = ?, rid = ? WHERE urid = ?';
            $bindParams = [
                $userRole->getAccid(),
                $userRole->getAppid(),
                $userRole->getUid(),
                $userRole->getRid(),
                $userRole->getUrid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete the user role.
     *
     * @param UserRole $userRole UserRole object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(UserRole $userRole): bool
    {
        $sql = 'DELETE FROM user_role WHERE urid = ?';
        $bindParams = [$userRole->getUrid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all user roles.
     *
     * @return array Array of mapped UserRole objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM user_role';
        $bindParams = [];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Find a user role by urid.
     *
     * @param int $urid
     *
     * @return UserRole
     *
     * @throws ApiException
     */
    public function findByUrid(int $urid): UserRole
    {
        $sql = 'SELECT * FROM user_role WHERE urid = ?';
        $bindParams = [$urid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Fetch a user role by uid, appid and rolename.
     *
     * @param integer $uid User ID.
     * @param integer $appid Application ID.
     * @param string $rolename Rolename.
     *
     * @return UserRole User role object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidAppidRolename(int $uid, int $appid, string $rolename): UserRole
    {
        $sql = 'SELECT ur.* FROM user_role ur';
        $sql .= ' INNER JOIN `role` `r` ON `ur`.`rid` = `r`.`rid`';
        $sql .= ' WHERE `ur`.`uid` = ?';
        $sql .= ' AND `r`.`name` = ?';
        $sql .= ' AND `ur`.`appid` = ?';
        $bindParams = [$uid, $rolename, $appid];

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Return whether a user has a specified role.
     *
     * @param integer $uid User ID.
     * @param string $rolename Role name.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function hasRole(int $uid, string $rolename): bool
    {
        $sql = 'SELECT * FROM user_role AS ur';
        $sql .= ' INNER JOIN role as r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid=?';
        $sql .= ' AND r.name=?';
        $bindParams = [$uid, $rolename];
        $rows = $this->fetchRows($sql, $bindParams);
        return !empty($rows);
    }

    /**
     * Fetch user roles by UID and role name.
     *
     * @param integer $uid User ID.
     * @param string $rolename Role name.
     *
     * @return array Array of UserRole objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidRolename(int $uid, string $rolename): array
    {
        $sql = 'SELECT * FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' WHERE ur.uid=?';
        $sql .= ' AND r.name=?';
        $bindParams = [$uid, $rolename];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Fetch user roles by UID that have a role in the array of role names.
     *   With potentially account/application validation.
     *
     * @param integer $uid User ID.
     * @param array $rolenames Role names.
     * @param string|null $accid account ID.
     *   If null, no account ID validation.
     * @param string|null $appid application ID.
     *   If null, no application ID validation.
     *
     * @return array Array of UserRole objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidRolenames(
        int $uid,
        array $rolenames,
        string $accid = null,
        string $appid = null
    ): array {
        $bindParams = [$uid];
        $sql = 'SELECT ur.*';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' WHERE ur.uid=?';
        $sql .= ' AND r.name in (?)';
        array_push($bindParams, implode(', ', $rolenames));
        if (!empty($accid)) {
            $sql .= ' AND ur.accid = ?';
            array_push($bindParams, $accid);
        }
        if (!empty($appid)) {
            $sql .= ' AND ur.appid = ?';
            array_push($bindParams, $appid);
        }

        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Return whether a user has a specified role in an account.
     *
     * @param integer $uid User ID.
     * @param integer $accid Account ID.
     * @param string $rolename Role name.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function hasAccidRole(int $uid, int $accid, string $rolename): bool
    {
        $sql = 'SELECT * FROM user_role AS ur';
        $sql .= ' INNER JOIN role as r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid=?';
        $sql .= ' AND accid=?';
        $sql .= ' AND r.name=?';
        $bindParams = [$uid, $accid, $rolename];
        $rows = $this->fetchRows($sql, $bindParams);
        return !empty($rows);
    }

    /**
     * Return whether a user has access to an application.
     *
     * @param integer $uid User ID.
     * @param integer $appid Application ID.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function hasAppidAccess(int $uid, int $appid): bool
    {
        $sql = 'SELECT * FROM user_role';
        $sql .= ' WHERE uid=?';
        $sql .= ' AND appid=?';
        $bindParams = [$uid, $appid];
        $rows = $this->fetchRows($sql, $bindParams);
        return !empty($rows);
    }

    /**
     * Find all user roles for a user ID.
     *
     * @param integer $uid User ID.
     *
     * @return array Array of UserRole objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid): array
    {
        $sql = 'SELECT * FROM user_role WHERE uid=?';
        $bindParams = [$uid];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Find user roles using filter.
     *
     * @param integer $uid User ID.
     * @param array $params Associative array of filter params.
     *
     * @return array User roles
     *
     * @throws ApiException Return an ApiException on DB error.
     *
     * @example
     *   findByFilter([
     *     'col' => ['rid' => 1],
     *     'order_by' => 'uid',
     *     'direction' => 'asc'
     *   )
     */
    public function findForUidWithFilter(int $uid, array $params): array
    {
        $sql = <<<SQL
SELECT *
FROM user_role AS ur
WHERE ur.urid IN (
    SELECT ur.urid
    FROM user_role AS ur
    WHERE EXISTS (
        SELECT ur.*
        FROM user_role AS ur
        INNER JOIN role AS r
        ON ur.rid = r.rid
        WHERE ur.uid = ?
        AND r.name = "Administrator"
    )
    UNION DISTINCT
    SELECT ur.urid
    FROM user_role AS ur
    WHERE ur.accid in (
        SELECT ur.accid
        FROM user_role AS ur
        INNER JOIN role AS r
        ON ur.rid = r.rid
        WHERE ur.uid = ?
        AND r.name = "Account manager"
    )
    UNION DISTINCT
    SELECT ur.urid
    FROM user_role AS ur
    WHERE ur.appid in (
        SELECT ur.appid
        FROM user_role AS ur
        INNER JOIN role AS r
        ON ur.rid = r.rid
        WHERE ur.uid = ?
        AND r.name = "Application manager"
    )
    UNION DISTINCT
    SELECT ur.urid
    FROM user_role AS ur
    INNER JOIN role AS r
    ON ur.rid = r.rid
    WHERE ur.uid = ?
)
SQL;

        $bindParams = [$uid, $uid, $uid, $uid];
        $where = $order = [];

        if (!empty($params['col'])) {
            foreach ($params['col'] as $col => $val) {
                if (empty($val)) {
                    $where[] = "isnull($col)";
                } else {
                    $where[] = "$col=?";
                    $bindParams[] = $val;
                }
            }
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        if (!empty($params['order_by'])) {
            $order['order_by'] = $params['order_by'];
        }
        if (!empty($params['direction'])) {
            $order['direction'] = $params['direction'];
        }

        return $this->fetchRows($sql, $bindParams, $order);
    }

    /**
     * Find user roles using filter.
     *
     * @param array $params Associative array of filter params.
     *
     * @return array User roles
     *
     * @throws ApiException Return an ApiException on DB error.
     *
     * @example
     *   findByFilter([
     *     'col' => ['rid' => 1],
     *     'order_by' => 'uid',
     *     'direction' => 'asc'
     *   )
     */
    public function findByFilter(array $params): array
    {
        $sql = 'SELECT * FROM user_role';
        $where = $bindParams = $order = [];

        if (!empty($params['col'])) {
            foreach ($params['col'] as $col => $val) {
                if (empty($val)) {
                    $where[] = "isnull($col)";
                } else {
                    $where[] = "$col=?";
                    $bindParams[] = $val;
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        if (!empty($params['order_by'])) {
            $order['order_by'] = $params['order_by'];
        }
        if (!empty($params['direction'])) {
            $order['direction'] = $params['direction'];
        }

        return $this->fetchRows($sql, $bindParams, $order);
    }

    /**
     * Find user roles for a user by accid and rolename.
     *
     * @param int $uid
     * @param int $accid
     * @param string $roleName
     *
     * @return array
     *
     * @throws ApiException
     */
    public function findByUidAccidRolename(int $uid, int $accid, string $roleName): array
    {
        $sql = <<<SQL
SELECT ur.*
FROM user_role AS ur
INNER JOIN role AS r
ON r.rid = ur.rid
WHERE ur.uid = ?
AND ur.accid = ?
AND r.name = ?
SQL;
        $bindParams = [$uid, $accid, $roleName];

        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a DB row to a UserRole object.
     *
     * @param array $row DB Row.
     *
     * @return UserRole UserRole object.
     */
    protected function mapArray(array $row): UserRole
    {
        $userRole = new UserRole();

        $userRole->setUrid($row['urid'] ?? null);
        $userRole->setAccid($row['accid'] ?? null);
        $userRole->setAppid($row['appid'] ?? null);
        $userRole->setUid($row['uid'] ?? null);
        $userRole->setRid($row['rid'] ?? null);

        return $userRole;
    }
}
