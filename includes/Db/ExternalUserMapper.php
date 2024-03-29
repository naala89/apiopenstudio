<?php

/**
 * Class ExternalUserMapper.
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
 * Class ExternalUserMapper.
 *
 * Mapper class for DB calls used for the external_user table.
 */
class ExternalUserMapper extends Mapper
{
    /**
     * Save an external user object.
     *
     * @param ExternalUser $user ExternalUser object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(ExternalUser $user): bool
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
     * @param ExternalUser $externalUser ExternalUser object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(ExternalUser $externalUser): bool
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
     * @return ExternalUser External user object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findById(int $id): ExternalUser
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
     * @return ExternalUser External user object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppIdEntityExternalId(int $appId, string $externalEntity, int $externalId): ExternalUser
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
    public function findByAppid(int $appId): array
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
     * @return ExternalUser ExternalUser object.
     */
    protected function mapArray(array $row): ExternalUser
    {
        $user = new ExternalUser();

        $user->setId($row['id'] ?? 0);
        $user->setAppId($row['appid'] ?? 0);
        $user->setExternalId($row['external_id'] ?? null);
        $user->setExternalEntity($row['external_entity'] ?? null);
        $user->setDataField1($row['data_field_1'] ?? null);
        $user->setDataField2($row['data_field_2'] ?? null);
        $user->setDataField3($row['data_field_3'] ?? null);

        return $user;
    }
}
