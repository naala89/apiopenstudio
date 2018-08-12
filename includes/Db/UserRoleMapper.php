<?php

/**
 * Fetch and save user_role data.
 */

namespace Datagator\Db;
use Datagator\Core;

class UserRoleMapper
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
   * @param \Datagator\Db\UserRole $userRole
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(UserRole $userRole)
  {
    if ($userRole->getUrid() == NULL) {
      $sql = 'INSERT INTO user_role (uid, rid, appid, accid) VALUES (?, ?, ?, ?)';
      $bindParams = array(
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getAppId(),
        $userRole->getAccId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE user_role SET uid=?, rid=?, appid=?, accid=? WHERE urid = ?';
      $bindParams = array(
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getAppId(),
        $userRole->getAccId(),
        $userRole->getUrid()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param \Datagator\Db\UserRole $userRole
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function delete(UserRole $userRole)
  {
    $sql = 'DELETE FROM user_role WHERE urid = ?';
    $bindParams = array($userRole->getUrid());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return true;
  }

  /**
   * @param $urid
   * @return \Datagator\Db\UserRole
   */
  public function findByUrid($urid)
  {
    $sql = 'SELECT * FROM user_role WHERE urid = ?';
    $bindParams = array($urid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $uid
   * @return array
   */
  public function findByUid($uid)
  {
    $sql = 'SELECT * FROM user_role WHERE uid = ?';
    $bindParams = array($uid);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param $appId
   * @return array
   */
  public function findByAppId($appId)
  {
    $sql = 'SELECT * FROM user_role WHERE appid = ?';
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
   * @param $accId
   * @return array
   */
  public function findByAccId($accId)
  {
    $sql = 'SELECT * FROM user_role WHERE accid = ?';
    $bindParams = array($accId);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param $rid
   * @return array
   */
  public function findByRid($rid)
  {
    $sql = 'SELECT * FROM user_role WHERE rid = ?';
    $bindParams = array($rid);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param $uid
   * @param $appId
   * @param $rid
   * @return \Datagator\Db\UserRole
   */
  public function findByUserAppRole($uid, $appId, $rid)
  {
    $sql = 'SELECT * FROM user_role WHERE uid = ? AND appid = ? AND rid = ?';
    $bindParams = array($uid, $appId, $rid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param null $uid
   * @param null $rid
   * @param null $appId
   * @param null $accId
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  public function findBy($uid=null, $rid=null, $appId=null, $accId=null)
  {
    if (empty($uid) && empty($appId) && empty($rid)) {
      throw new Core\ApiException('cannot search for user role without at least user, role or application');
    }
    $sqlWhere = array();
    $bindParams = array();
    if (!empty($uid)) {
      $sqlWhere[] = 'uid = ?';
      $bindParams[] = $uid;
    }
    if (!empty($rid)) {
      $sqlWhere[] = 'rid = ?';
      $bindParams[] = $rid;
    }
    if (!empty($accId)) {
      $sqlWhere[] = 'accid = ?';
      $bindParams[] = $accId;
    }
    $sql = 'SELECT * FROM user_role WHERE ' . implode(' AND ', $sqlWhere);

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
   * @return \Datagator\Db\UserRole
   */
  protected function mapArray(array $row)
  {
    $userRole = new UserRole();

    $userRole->setId(!empty($row['urid']) ? $row['urid'] : NULL);
    $userRole->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $userRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $userRole->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $userRole->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);

    return $userRole;
  }
}
