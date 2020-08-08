<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;

/**
 * Class UserRoleMapper.
 *
 * @package Gaterdata\Db
 */
class UserRoleMapper extends Mapper
{

    /**
     * Save the user role.
     *
     * @param UserRole $userRole
     *   UserRole object.
     *
     * @return bool
     *   Result of the save.
     *
     * @throws ApiException
     */
    public function save(UserRole $userRole)
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
            $sql = 'UPDATE user_role SET (accid, appid, uid, rid) WHERE urid = ?';
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
     * @param UserRole $userRole
     *   UserRole object.
     *
     * @return bool
     *   Success.
     *
     * @throws ApiException
     */
    public function delete(UserRole $userRole)
    {
        $sql = 'DELETE FROM user_role WHERE urid = ?';
        $bindParams = [$userRole->getUrid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all user roles.
     *
     * @return array
     *   Array of mapped UserRole objects.
     *
     * @throws ApiException
     */
    public function findAll()
    {
        $sql = 'SELECT * FROM user_role';
        $bindParams = [];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Fetch a user role by uid, appid and rolename.
     *
     * @param integer $uid
     *   User ID.
     * @param integer $appid
     *   Application ID.
     * @param string $rolename
     *   Rolename.
     *
     * @return UserRole
     *   User role object.
     *
     * @throws ApiException
     */
    public function findByUidAppidRolename($uid, $appid, $rolename)
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
     * @param Integer $uid
     *   User ID.
     * @param String $rolename
     *   Role name.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function hasRole($uid, $rolename) {
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
     * @param integer $uid
     *   User ID.
     * @param string $rolename
     *   Role name.
     *
     * @return array
     *   Array of UserRole objects.
     *
     * @throws ApiException
     */
    public function findByUidRolename($uid, $rolename) {
        $sql = 'SELECT * FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' WHERE ur.uid=?';
        $sql .= ' AND r.name=?';
        $bindParams = [$uid, $rolename];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Return whether a user has a specified role in an account.
     *
     * @param integer $uid
     *   User ID.
     * @param integer $accid
     *   Account ID.
     * @param string $rolename
     *   Role name.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function hasAccidRole($uid, $accid, $rolename) {
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
     * Find all user roles for a user ID.
     *
     * @param integer $uid
     *   User ID.
     *
     * @return array
     *   Array of UserRole objects.
     *
     * @throws ApiException
     */
    public function findByUid($uid) {
        $sql = 'SELECT * FROM user_role WHERE uid=?';
        $bindParams = [$uid];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Find user roles using filter.
     *
     * @param array $params
     *  Associative array of filter params.
     * @return array
     *   User roles
     *
     * @throws ApiException
     *
     * @example
     *   findByFilter([
     *     'col' => ['rid' => 1],
     *     'order_by' => 'uid',
     *     'direction' => 'asc'
     *   )
     */
    public function findForUidWithFilter($uid, $params)
    {
        $priviligedRoles = ["Administrator", "Account manager", "Application manager"];
        $sql = 'SELECT *';
        $sql .= ' FROM user_role';
        $sql .= ' WHERE urid';
        $sql .= ' IN (';
        $sql .= ' SELECT ur.urid';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' WHERE EXISTS (';
        $sql .= ' SELECT *';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' AND r.name IN ("' . implode('", "', $priviligedRoles) . '")';
        $sql .= ' AND ur.uid = ?)';
        $sql .= ' UNION DISTINCT';
        $sql .= ' SELECT ur.urid';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND NOT EXISTS (';
        $sql .= ' SELECT *';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' AND r.name IN ("' . implode('", "', $priviligedRoles) . '")';
        $sql .= ' AND ur.uid = ?))';

        $bindParams = [$uid, $uid, $uid];
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
     * @param array $params
     *  Associative array of filter params.
     * @return array
     *   User roles
     *
     * @throws ApiException
     *
     * @example
     *   findByFilter([
     *     'col' => ['rid' => 1],
     *     'order_by' => 'uid',
     *     'direction' => 'asc'
     *   )
     */
    public function findByFilter($params)
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
     * Map a DB row to a UserRole object.
     *
     * @param array $row
     *   DB Row.
     *
     * @return UserRole
     *   UserRole object.
     */
    protected function mapArray(array $row)
    {
        $userRole = new UserRole();

        $userRole->setUrid(!empty($row['urid']) ? $row['urid'] : null);
        $userRole->setAccid(!empty($row['accid']) ? $row['accid'] : null);
        $userRole->setAppid(!empty($row['appid']) ? $row['appid'] : null);
        $userRole->setUid(!empty($row['uid']) ? $row['uid'] : null);
        $userRole->setRid(!empty($row['rid']) ? $row['rid'] : null);

        return $userRole;
    }
}
