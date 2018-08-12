<?php

/**
 * Fetch and save external_user data.
 */

namespace Datagator\Db;
use Datagator\Core;

class ExternalUserMapper
{
  protected $db;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\ExternalUser $user
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(ExternalUser $user)
  {
    if ($user->getId() == NULL) {
      $sql = 'INSERT INTO external_user (appid, external_id, external_entity, data_field_1, data_field_2, data_field_3) VALUES (?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $user->getAppId(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE external_user SET appid = ?, external_id = ?, external_entity = ?, data_field_1 = ?, data_field_2 = ?, data_field_3 = ? WHERE id = ?';
      $bindParams = array(
        $user->getAppId(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3(),
        $user->getId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param $id
   * @return \Datagator\Db\ExternalUser
   */
  public function findById($id)
  {
    $sql = 'SELECT * FROM external_user WHERE id = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $appId
   * @param $externalEntity
   * @param $externalId
   * @return \Datagator\Db\ExternalUser
   */
  public function findByAppIdEntityExternalId($appId, $externalEntity, $externalId)
  {
    $sql = 'SELECT * FROM external_user WHERE appid = ? AND external_entity = ? AND external_id = ?';
    $bindParams = array($appId, $externalEntity, $externalId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $appId
   * @return array
   */
  public function findByCid($appId)
  {
    $sql = 'SELECT * FROM external_user WHERE appid = ?';
    $bindParams = array($appId);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param array $row
   * @return \Datagator\Db\ExternalUser
   */
  protected function mapArray(array $row)
  {
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
