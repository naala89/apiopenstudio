<?php
/**
 * Class VarStoreMapper.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class VarStoreMapper.
 *
 * Mapper class for DB calls used for the var_store table.
 */
class VarStoreMapper extends Mapper
{
    /**
     * Save the var.
     *
     * @param \Gaterdata\Db\VarStore $varStore VarStore object.
     *
     * @return boolean Success.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function save(VarStore $varStore)
    {
        if ($varStore->getVid() == null) {
            $sql = 'INSERT INTO `var_store` (`appid`, `key`, `val`) VALUES (?, ?, ?)';
            $bindParams = [
                $varStore->getAppid(),
                $varStore->getKey(),
                $varStore->getVal(),
            ];
        } else {
            $sql = 'UPDATE `var_store` SET `appid`=?, `key`=?, `val`=? WHERE `vid` = ?';
            $bindParams = [
                $varStore->getAppid(),
                $varStore->getKey(),
                $varStore->getVal(),
                $varStore->getVid(),
            ];
        }

        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete the vars.
     *
     * @param \Gaterdata\Db\VarStore $varStore VarStore object.
     *
     * @return boolean Success.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function delete(VarStore $varStore)
    {
        if ($varStore->getVid() === null) {
            throw new ApiException('cannot delete var - empty ID', 2);
        }
        $sql = 'DELETE FROM `var_store` WHERE `vid` = ?';
        $bindParams = [$varStore->getVid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find a var by its ID.
     *
     * @param integer $vid Var ID.
     *
     * @return array Array of varStore objects.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByVId(int $vid)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `vid` = ?';
        $bindParams = [$vid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Return all vars.
     *
     * @param array $params Filter params.
     *
     * @return array Array of varStore objects.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = [])
    {
        $sql = 'SELECT * FROM var_store';

        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Return all vars that a a user has access to.
     *
     * @param integer $uid User ID.
     * @param array $params Filter params.
     *
     * @return array Array of varStore objects.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid, array $params = [])
    {
        $sql = 'SELECT *';
        $sql .= ' FROM var_store';
        $sql .= ' WHERE appid IN (';
        $sql .= ' SELECT DISTINCT appid';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name IN ("Application manager", "Developer"))';
        $bindParams = [$uid];

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find a var by vid with user/role validation against the var's application.
     *
     * @param integer $uid User ID.
     * @param array $roles Role IDs.
     * @param integer $vid Var ID.
     *
     * @return array Array of varStore objects.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByUidRolesVid(int $uid, array $roles, int $vid)
    {
        $sql = 'SELECT vs.* FROM `var_store` AS vs';
        $sql .= ' INNER JOIN `user_role` AS ur ON vs.`appid` = ur.`appid`';
        $sql .= ' INNER JOIN `role` AS r ON  ur.`rid` = r.`rid`';
        $sql .= ' WHERE ur.`uid` = ?';
        $sql .= ' AND vs.`vid` = ?';
        $bindParams = [$uid, $vid];
        $placeholders = [];
        foreach ($roles as $role) {
            $placeholders[] = '?';
            $bindParams[] = $role;
        }
        $sql .= ' AND r.`name` in (' . implode(', ', $placeholders) . ')';

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find all vars with user/role validation against the vars's application.
     *
     * @param integer $uid User ID.
     * @param array $roles Role names.
     * @param array $params Keyword, order and direction.
     *
     * @return array
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByUidRolesAll(int $uid, array $roles, array $params)
    {
        $sql = 'SELECT vs.* FROM `var_store` AS vs';
        $sql .= ' INNER JOIN `user_role` AS ur ON vs.`appid` = ur.`appid`';
        $sql .= ' INNER JOIN `role` AS r ON  ur.`rid` = r.`rid`';
        $sql .= ' WHERE ur.`uid` = ?';
        $bindParams = [$uid];
        $placeholders = [];
        foreach ($roles as $role) {
            $placeholders[] = '?';
            $bindParams[] = $role;
        }
        $sql .= ' AND r.`name` in (' . implode(', ', $placeholders) . ')';

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find a var by application ID and var key.
     *
     * @param integer $appId Application ID.
     * @param string $key Var key.
     *
     * @return VarStore VarStore object.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAppIdKey(int $appId, string $key)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `appid` = ? AND `key` = ?';
        $bindParams = [$appId, $key];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find the vars belonging to an application.
     *
     * @param integer $appId Application ID.
     *
     * @return array Array of VarStore objects.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAppId(int $appId)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `appid` = ?';
        $bindParams = [$appId];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a results row to attributes.
     *
     * @param array $row DB results row.
     *
     * @return VarStore Mapped row.
     */
    protected function mapArray(array $row)
    {
        $varStore = new VarStore();

        $varStore->setVid(!empty($row['vid']) ? $row['vid'] : 0);
        $varStore->setAppid(!empty($row['appid']) ? $row['appid'] : 0);
        $varStore->setKey(!empty($row['key']) ? $row['key'] : '');
        $varStore->setVal(!empty($row['val']) ? $row['val'] : '');

        return $varStore;
    }
}
