<?php

namespace Gaterdata\Db;

/**
 * Class VarStoreMapper.
 *
 * @package Gaterdata\Db
 */
class VarStoreMapper extends Mapper
{
    /**
     * Save the var.
     *
     * @param \Gaterdata\Db\VarStore $varStore
     *   VarStore object.
     *
     * @return bool
     *   Success.
     *
     * @throws \Gaterdata\Core\ApiException
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
     * @param \Gaterdata\Db\VarStore $varStore
     *   VarStore object.
     *
     * @return bool
     *   Success.
     *
     * @throws \Gaterdata\Core\ApiException
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
     * @param int $vid
     *   Var ID.
     *
     * @return array
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByVId($vid)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `vid` = ?';
        $bindParams = [$vid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a var by vid with user/role validation against the var's application.
     *
     * @param integer $uid
     *   User ID.
     * @param string $roles
     *   Role IDs
     * @param $vid
     *   Var ID.
     *
     * @return array
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByUidRolesVid($uid, $roles, $vid)
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
     * @param integer $uid
     *   User ID.
     * @param array $roles
     *   Role names.
     * @param array $params
     *   keyword, order and direction.
     *
     * @return array
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByUidRolesAll($uid, $roles, $params)
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
     * @param int $appId
     *   Application ID.
     * @param string $key
     *   Var key.
     *
     * @return Mixed
     *   VarStore object.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByAppIdKey($appId, $key)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `appid` = ? AND `key` = ?';
        $bindParams = [$appId, $key];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find the vars belonging to an application.
     *
     * @param int $appId
     *   Application ID.
     *
     * @return array
     *   Array of VarStore objects.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByAppId($appId)
    {
        $sql = 'SELECT * FROM `var_store` WHERE `appid` = ?';
        $bindParams = [$appId];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a results row to attributes.
     *
     * @param array $row
     *   DB results row.
     *
     * @return VarStore
     *   Mapped row.
     */
    protected function mapArray(array $row)
    {
        $varStore = new VarStore();

        $varStore->setVid(!empty($row['vid']) ? $row['vid'] : null);
        $varStore->setAppid(!empty($row['appid']) ? $row['appid'] : null);
        $varStore->setKey(!empty($row['key']) ? $row['key'] : null);
        $varStore->setVal(!empty($row['val']) ? $row['val'] : null);

        return $varStore;
    }
}
