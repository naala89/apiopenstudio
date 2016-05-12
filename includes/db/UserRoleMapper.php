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
    if ($userRole->getId() == NULL) {
      $sql = 'INSERT INTO user_role (uid, rid, appid) VALUES (?, ?, ?)';
      $bindParams = array(
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getAppId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE user_role SET uid=?, rid=?, appid=? WHERE id = ?';
      $bindParams = array(
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getAppId(),
        $userRole->getId()
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
   * @return \Datagator\Db\UserRole
   */
  public function findById($id)
  {
    $sql = 'SELECT * FROM user_role WHERE id = ?';
    $bindParams = array($id);
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
      $userRole = $this->mapArray($recordSet->fields);
      $entries[] = $userRole->debug();
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
      $userRole = $this->mapArray($recordSet->fields);
      $entries[] = $userRole->debug();
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
      $userRole = $this->mapArray($recordSet->fields);
      $entries[] = $userRole->debug();
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
   * @param null $appId
   * @param null $rid
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  public function findByMixed($uid=null, $appId=null, $rid=null)
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
    if (!empty($appId)) {
      $sqlWhere[] = 'appid = ?';
      $bindParams[] = $appId;
    }
    if (!empty($rid)) {
      $sqlWhere[] = 'rid = ?';
      $bindParams[] = $rid;
    }
    $sql = 'SELECT * FROM user_role WHERE ' . implode(' AND ', $sqlWhere);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $userRole = $this->mapArray($recordSet->fields);
      $entries[] = $userRole->debug();
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

    $userRole->setId(!empty($row['id']) ? $row['id'] : NULL);
    $userRole->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $userRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);
    $userRole->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);

    return $userRole;
  }
}
