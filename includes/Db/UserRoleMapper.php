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
     * Find user roles using filter.
     *
     * @param array $params
     *  Associative array of filter poarams.
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
