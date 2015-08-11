<?php

/**
 * Fetch and save external_user data.
 */

namespace Datagator\Db;
use Datagator\Core\ApiException;
use Datagator\Core\Debug;

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
    if ($user->getUid() == NULL) {
      $sql = 'INSERT INTO external_user (`cid`, `external_id`, `external_entity`, `data_field_1`, `data_field_2`, `data_field_3`) VALUES (?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $user->getCid(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3()
      );
      $result = $this->db->Execute($sql, $bindParams);
      if (!$result) {
        throw new ApiException('error inserting external user');
      }
    } else {
      $sql = 'UPDATE external_user SET `cid` = ?, `external_id` = ?, `external_entity` = ?, `data_field_1` = ?, `data_field_2` = ?, `data_field_3` = ? WHERE `uid` = ?';
      $bindParams = array(
        $user->getCid(),
        $user->getExternalId(),
        $user->getExternalEntity(),
        $user->getDataField1(),
        $user->getDataField2(),
        $user->getDataField3(),
        $user->getUid()
      );
      $result = $this->db->Execute($sql, $bindParams);
      if (!$result) {
        throw new ApiException($this->db->ErrorMsg());
      }
    }
    return TRUE;
  }

  /**
   * @param $uid
   * @return \Datagator\Db\ExternalUser
   */
  public function findByUid($uid)
  {
    $sql = 'SELECT * FROM external_user WHERE `uid` = ?';
    $bindParams = array($uid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $cid
   * @param $externalEntity
   * @param $externalId
   * @return \Datagator\Db\ExternalUser
   */
  public function findByCidEntityExternalId($cid, $externalEntity, $externalId)
  {
    $sql = 'SELECT * FROM external_user WHERE `cid` = ? AND `external_entity` = ? AND `external_id` = ?';
    $bindParams = array($cid, $externalEntity, $externalId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $cid
   * @return array
   */
  public function findByCid($cid)
  {
    $sql = 'SELECT * FROM external_user WHERE `cid` = ?';
    $bindParams = array($cid);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param array $row
   * @return \Datagator\Db\ExternalUser
   * @throws \Datagator\Core\ApiException
   */
  protected function mapArray(array $row)
  {
    $user = new ExternalUser();

    $user->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $user->setCid(!empty($row['cid']) ? $row['cid'] : NULL);
    $user->setExternalId(!empty($row['external_id']) ? $row['external_id'] : NULL);
    $user->setExternalEntity(!empty($row['external_entity']) ? $row['external_entity'] : NULL);
    $user->setDataField1(!empty($row['data_field_1']) ? $row['data_field_1'] : NULL);
    $user->setDataField2(!empty($row['data_field_2']) ? $row['data_field_2'] : NULL);
    $user->setDataField3(!empty($row['data_field_3']) ? $row['data_field_3'] : NULL);

    return $user;
  }
}
