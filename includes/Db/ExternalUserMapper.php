<?php
/**
 * Class ExternalUserMapper.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;

/**
 * Class ExternalUserMapper.
 *
 * Mapper class for DB calls used for the external_user table.
 */
class ExternalUserMapper extends Mapper
{

    /**
     * Save an external user object.
     *
     * @param \Gaterdata\Db\ExternalUser $user ExternalUser object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(ExternalUser $user)
    {
        if ($user->getId() == null) {
            $sql = 'INSERT INTO external_user (appid, external_id, external_entity, data_field_1, data_field_2, ';
            $sql .= 'data_field_3) VALUES (?, ?, ?, ?, ?, ?)';
            $bindParams = [
            $user->getAppId(),
            $user->getExternalId(),
            $user->getExternalEntity(),
            $user->getDataField1(),
            $user->getDataField2(),
            $user->getDataField3(),
            ];
        } else {
            $sql = 'UPDATE external_user SET appid = ?, external_id = ?, external_entity = ?, data_field_1 = ?, ';
            $sql .= 'data_field_2 = ?, data_field_3 = ? WHERE id = ?';
            $bindParams = [
            $user->getAppId(),
            $user->getExternalId(),
            $user->getExternalEntity(),
            $user->getDataField1(),
            $user->getDataField2(),
            $user->getDataField3(),
            $user->getId(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete an external user.
     *
     * @param \Gaterdata\Db\ExternalUser $externalUser ExternalUser object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(ExternalUser $externalUser)
    {
        $sql = 'DELETE FROM external_user WHERE id = ?';
        $bindParams = [$externalUser->getId()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find an external user by ID.
     *
     * @param integer $id External user ID.
     *
     * @return \Gaterdata\Db\ExternalUser External user object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findById(int $id)
    {
        $sql = 'SELECT * FROM external_user WHERE id = ?';
        $bindParams = [$id];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an external user by app ID, external entity name and external ID.
     *
     * @param integer $appId Application ID.
     * @param string $externalEntity External entity name.
     * @param integer $externalId External ID.
     *
     * @return \Gaterdata\Db\ExternalUser External user object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppIdEntityExternalId(int $appId, string $externalEntity, int $externalId)
    {
        $sql = 'SELECT * FROM external_user WHERE appid = ? AND external_entity = ? AND external_id = ?';
        $bindParams = [
            $appId,
            $externalEntity,
            $externalId,
        ];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an external user by application ID.
     *
     * @param integer $appId Application ID.
     *
     * @return array External user object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppid(int $appId)
    {
        $sql = 'SELECT * FROM external_user WHERE appid = ?';
        $bindParams = [$appId];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a DB results row to this object.
     *
     * @param array $row DB row results object.
     *
     * @return \Gaterdata\Db\ExternalUser ExternalUser object.
     */
    protected function mapArray(array $row)
    {
        $user = new ExternalUser();

        $user->setId(!empty($row['id']) ? $row['id'] : 0);
        $user->setAppId(!empty($row['appid']) ? $row['appid'] : 0);
        $user->setExternalId(!empty($row['external_id']) ? $row['external_id'] : null);
        $user->setExternalEntity(!empty($row['external_entity']) ? $row['external_entity'] : null);
        $user->setDataField1(!empty($row['data_field_1']) ? $row['data_field_1'] : null);
        $user->setDataField2(!empty($row['data_field_2']) ? $row['data_field_2'] : null);
        $user->setDataField3(!empty($row['data_field_3']) ? $row['data_field_3'] : null);

        return $user;
    }
}
