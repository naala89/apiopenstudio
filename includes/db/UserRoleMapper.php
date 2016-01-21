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
    if ($userRole->getRid() == NULL) {
      $sql = 'INSERT INTO user_role (`uid`, `rid`, `appid`) VALUES (?, ?, ?)';
      $bindParams = array(
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getAppId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE user_role SET `uid`=?, `rid`=?, `appid`=? WHERE `id` = ?';
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
    $sql = 'SELECT * FROM user_role WHERE `id` = ?';
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
    $sql = 'SELECT * FROM user_role WHERE `uid` = ?';
    $bindParams = array($uid);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $roles   = array();
    while (!$recordSet->EOF) {
      $roles[] = $this->mapArray($recordSet->fields);
    }

    return $roles;
  }

  /**
   * @param $email
   * @return array
   */
  public function findByEmail($email)
  {
    $sql = 'SELECT ur.* FROM user_role ur INNER JOIN `user` u ON u.uid=ur.uid WHERE u.email = ?';
    $bindParams = array($email);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $rid
   * @return array
   */
  public function findByRid($rid)
  {
    $sql = 'SELECT * FROM user_role WHERE `rid` = ?';
    $bindParams = array($rid);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $appId
   * @return array
   */
  public function findByAppId($appId)
  {
    $sql = 'SELECT * FROM user_role WHERE `appid` = ?';
    $bindParams = array($appId);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
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
