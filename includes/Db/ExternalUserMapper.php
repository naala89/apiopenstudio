<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use ADOConnection;

/**
 * Class ExternalUserMapper.
 *
 * @package Gaterdata\Db
 */
class ExternalUserMapper extends Mapper {

  /**
   * ExternalUserMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an external user object.
   *
   * @param \Gaterdata\Db\ExternalUser $user
   *   ExternalUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function save(ExternalUser $user) {
    if ($user->getId() == NULL) {
      $sql = 'INSERT INTO external_user (appid, external_id, external_entity, data_field_1, data_field_2, data_field_3) VALUES (?, ?, ?, ?, ?, ?)';
      $bindParams = [
        $user->getAppId(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3(),
      ];
    }
    else {
      $sql = 'UPDATE external_user SET appid = ?, external_id = ?, external_entity = ?, data_field_1 = ?, data_field_2 = ?, data_field_3 = ? WHERE id = ?';
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
   * @param \Gaterdata\Db\ExternalUser $externalUser
   *   ExternalUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function delete(ExternalUser $externalUser) {
    $sql = 'DELETE FROM external_user WHERE id = ?';
    $bindParams = [$externalUser->getId()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find an external user by ID.
   *
   * @param int $id
   *   External user ID.
   *
   * @return \Gaterdata\Db\ExternalUser
   *   External user object.
   *
   * @throws ApiException
   */
  public function findById($id) {
    $sql = 'SELECT * FROM external_user WHERE id = ?';
    $bindParams = [$id];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find an external user by app ID, external entity name and external ID.
   *
   * @param int $appId
   *   Application ID.
   * @param string $externalEntity
   *   External entity name.
   * @param int $externalId
   *   External ID.
   *
   * @return \Gaterdata\Db\ExternalUser
   *   External user object.
   *
   * @throws ApiException
   */
  public function findByAppIdEntityExternalId($appId, $externalEntity, $externalId) {
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
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   External user object.
   *
   * @throws ApiException
   */
  public function findByAppid($appId) {
    $sql = 'SELECT * FROM external_user WHERE appid = ?';
    $bindParams = [$appId];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB results row to this object.
   *
   * @param array $row
   *   DB row results object.
   *
   * @return \Gaterdata\Db\ExternalUser
   *   ExternalUser object.
   */
  protected function mapArray(array $row) {
    $user = new ExternalUser();

    $user->setId(!empty($row['id']) ? $row['id'] : NULL);
    $user->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $user->setExternalId(!empty($row['external_id']) ? $row['external_id'] : NULL);
    $user->setExternalEntity(!empty($row['external_entity']) ? $row['external_entity'] : NULL);
    $user->setDataField1(!empty($row['data_field_1']) ? $row['data_field_1'] : NULL);
    $user->setDataField2(!empty($row['data_field_2']) ? $row['data_field_2'] : NULL);
    $user->setDataField3(!empty($row['data_field_3']) ? $row['data_field_3'] : NULL);

    return $user;
  }

}
