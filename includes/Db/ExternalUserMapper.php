<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use Cascade\Cascade;

/**
 * Class ExternalUserMapper.
 *
 * @package Datagator\Db
 */
class ExternalUserMapper {

  protected $db;

  /**
   * ExternalUserMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save an external user object.
   *
   * @param \Datagator\Db\ExternalUser $user
   *   ExternalUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(ExternalUser $user) {
    if ($user->getId() == NULL) {
      $sql = 'INSERT INTO external_user (appid, external_id, external_entity, data_field_1, data_field_2, data_field_3) VALUES (?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $user->getAppId(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3(),
      );
    }
    else {
      $sql = 'UPDATE external_user SET appid = ?, external_id = ?, external_entity = ?, data_field_1 = ?, data_field_2 = ?, data_field_3 = ? WHERE id = ?';
      $bindParams = array(
        $user->getAppId(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3(),
        $user->getId(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Delete an external user.
   *
   * @param \Datagator\Db\ExternalUser $externalUser
   *   ExternalUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(ExternalUser $externalUser) {
    $sql = 'DELETE FROM external_user WHERE id = ?';
    $bindParams = array($externalUser->getId());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find an external user by ID.
   *
   * @param int $id
   *   External user ID.
   *
   * @return \Datagator\Db\ExternalUser
   *   External user object.
   *
   * @throws ApiException
   */
  public function findById($id) {
    $sql = 'SELECT * FROM external_user WHERE id = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
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
   * @return \Datagator\Db\ExternalUser
   *   External user object.
   *
   * @throws ApiException
   */
  public function findByAppIdEntityExternalId($appId, $externalEntity, $externalId) {
    $sql = 'SELECT * FROM external_user WHERE appid = ? AND external_entity = ? AND external_id = ?';
    $bindParams = array($appId, $externalEntity, $externalId);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
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
    $bindParams = array($appId);

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Map a DB results row to this object.
   *
   * @param array $row
   *   DB row results object.
   *
   * @return \Datagator\Db\ExternalUser
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
