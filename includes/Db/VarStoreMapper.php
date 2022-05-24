<?php

/**
 * Class VarStoreMapper.
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
 * Class VarStoreMapper.
 *
 * Mapper class for DB calls used for the var_store table.
 */
class VarStoreMapper extends Mapper
{
    /**
     * Save the var.
     *
     * @param VarStore $varStore VarStore object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(VarStore $varStore): bool
    {
        if ($varStore->getVid() == null) {
            $sql = 'INSERT INTO `var_store` (`accid`, `appid`, `key`, `val`) VALUES (?, ?, ?, ?)';
            $bindParams = [
                $varStore->getAccid(),
                $varStore->getAppid(),
                $varStore->getKey(),
                $varStore->getVal(),
            ];
        } else {
            $sql = 'UPDATE `var_store` SET `accid`=?, `appid`=?, `key`=?, `val`=? WHERE `vid` = ?';
            $bindParams = [
                $varStore->getAccid(),
                $varStore->getAppid(),
                $varStore->getKey(),
                $varStore->getVal(),
                $varStore->getVid(),
            ];
        }

        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete a var.
     *
     * @param VarStore $varStore VarStore object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(VarStore $varStore): bool
    {
        if ($varStore->getVid() === null) {
            throw new ApiException('cannot delete var - empty ID', 2);
        }
        $sql = 'DELETE FROM `var_store` WHERE `vid` = ?';
        $bindParams = [$varStore->getVid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete multiple vars.
     *
     * @param $vars[] $varStore
     *   Array of VarStore object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function deleteMultiple(array $vars): bool
    {
        $bindParams = [];
        $vidPlaceholders = [];
        foreach ($vars as $var) {
            $bindParams[] = $var->getVid();
            $vidPlaceholders[] = '?';
        }
        $sql = 'DELETE FROM `var_store` WHERE `vid` IN (' . implode(', ', $vidPlaceholders) . ')';
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find a var by its ID.
     *
     * @param integer $vid Var ID.
     *
     * @return VarStore Find a varStore by its ID.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByVid(int $vid): VarStore
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
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = []): array
    {
        $sql = 'SELECT * FROM `var_store`';

        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Return all vars that a user has access to.
     *
     * @param integer $uid User ID.
     * @param array $params Filter params.
     *
     * @return array Array of varStore objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid, array $params = []): array
    {
        $sql = <<<QUERY
SELECT vs.*
FROM var_store AS vs 
WHERE EXISTS (
	SELECT ur.urid
	FROM user_role AS ur
	INNER JOIN role AS r ON ur.rid = r.rid
	WHERE r.name = "Administrator"
	AND ur.uid = ?
)
UNION DISTINCT
SELECT vs.*
FROM var_store AS vs 
WHERE vs.accid in (
	SELECT ur.accid
	FROM user_role AS ur
	INNER JOIN role AS r ON ur.rid = r.rid
	WHERE r.name != "Administrator"
	AND ur.uid = ?
)
UNION DISTINCT
SELECT vs.*
FROM var_store AS vs
WHERE vs.appid IN (
	SELECT app.appid
	FROM application AS app
	WHERE app.accid IN (
		SELECT ur.accid
		FROM user_role AS ur
		INNER JOIN role AS r ON ur.rid = r.rid
		WHERE r.name = "Account manager"
		AND ur.uid = ?
	)
)
UNION DISTINCT
SELECT vs.*
FROM var_store AS vs 
WHERE vs.appid in (
	SELECT ur.appid
	FROM user_role AS ur
	INNER JOIN role AS r ON ur.rid = r.rid
	WHERE r.name NOT IN ("Administrator", "Account manager")
	AND ur.uid = ?
)
QUERY;

        $bindParams = [$uid, $uid, $uid, $uid];

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Return a var by vid that a user has access to.
     *
     * @param integer $uid User ID.
     * @param integer $vid var ID.
     *
     * @return VarStore A VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidVid(int $uid, int $vid): VarStore
    {
        $sql = <<<QUERY
SELECT vs.* FROM var_store AS vs WHERE  vs.vid = ? AND vs.appid in (
    SELECT app.appid FROM application AS app WHERE (
        SELECT ur.urid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid 
            WHERE r.name = "Administrator" AND ur.uid = ?)
    UNION ALL
    SELECT app.appid FROM application AS app WHERE app.accid IN (
        SELECT ur.accid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid
            WHERE r.name = "Account manager" AND ur.uid = ?
    )
    UNION ALL
    SELECT app.appid FROM application AS app WHERE app.appid IN (
        SELECT ur.appid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid WHERE ur.uid = ?
    )
)
QUERY;

        $bindParams = [$vid, $uid, $uid, $uid];

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Return a var by appid/key that a user has access to.
     *
     * @param integer $uid User ID.
     * @param integer $appid application ID.
     * @param string $key var key.
     *
     * @return VarStore A VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidAppidKey(int $uid, int $appid, string $key): VarStore
    {
        $sql = <<<QUERY
SELECT vs.* FROM var_store AS vs WHERE vs.key = ? AND vs.appid = ? AND vs.appid in (
    SELECT app.appid FROM application AS app WHERE (
        SELECT ur.urid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid 
            WHERE r.name = "Administrator" AND ur.uid = ?)
    UNION ALL
    SELECT app.appid FROM application AS app WHERE app.accid IN (
        SELECT ur.accid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid
            WHERE r.name = "Account manager" AND ur.uid = ?
    )
    UNION ALL
    SELECT app.appid FROM application AS app WHERE app.appid IN (
        SELECT ur.appid FROM user_role AS ur INNER JOIN role AS r ON ur.rid = r.rid WHERE ur.uid = ?
    )
)
QUERY;

        $bindParams = [$key, $appid, $uid, $uid, $uid];

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a var by App ID and key with user/role validation.
     *
     * @param integer $uid User ID.
     * @param array $roles Role IDs.
     * @param integer $appid App ID.
     * @param string $key variable key.
     *
     * @return VarStore A VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidRolesAppidKey(int $uid, array $roles, int $appid, string $key): VarStore
    {
        $sql = 'SELECT vs.* FROM `var_store` AS vs';
        $sql .= ' INNER JOIN `user_role` AS ur ON vs.`appid` = ur.`appid`';
        $sql .= ' INNER JOIN `role` AS r ON ur.`rid` = r.`rid`';
        $sql .= ' WHERE ur.`uid` = ?';
        $sql .= ' AND vs.`appid` = ?';
        $sql .= ' AND vs.`key` = ?';
        $bindParams = [$uid, $appid, $key];
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
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidRolesAll(int $uid, array $roles, array $params): array
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
     * Find a var by account ID and var key.
     *
     * @param integer $accId Account ID.
     * @param string $key Var key.
     *
     * @return VarStore VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAccidKey(int $accId, string $key): VarStore
    {
        $sql = 'SELECT * FROM `var_store` WHERE `key` = ? AND `accid` = ?';
        $bindParams = [$key, $accId];

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a var by account ID and var key.
     *
     * @param int $appId Application ID.
     * @param string $key Var key.
     *
     * @return VarStore VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppidKey(int $appId, string $key): VarStore
    {
        $sql = 'SELECT * FROM `var_store` WHERE `key` = ? AND `appid` = ?';
        $bindParams = [$key, $appId];

        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find the vars belonging to an application.
     *
     * @param integer $appId Application ID.
     *
     * @return VarStore VarStore object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppId(int $appId): VarStore
    {
        $sql = 'SELECT * FROM `var_store` WHERE `appid` = ?';
        $bindParams = [$appId];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Map a results row to attributes.
     *
     * @param array $row DB results row.
     *
     * @return VarStore Mapped row.
     */
    protected function mapArray(array $row): VarStore
    {
        $varStore = new VarStore();

        $varStore->setVid($row['vid'] ?? 0);
        $varStore->setAccid($row['accid'] ?? null);
        $varStore->setAppid($row['appid'] ?? null);
        $varStore->setKey($row['key'] ?? null);
        $varStore->setVal($row['val'] ?? null);

        return $varStore;
    }
}
