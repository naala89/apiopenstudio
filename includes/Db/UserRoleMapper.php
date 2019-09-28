<?php

namespace Gaterdata\Db;

/**
 * Class UserRoleMapper.
 *
 * @package Gaterdata\Db
 */
class UserRoleMapper extends Mapper {

  /**
   * Save the user role.
   *
   * @param \Gaterdata\Db\UserRole $userRole
   *   UserRole object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function save(UserRole $userRole) {
    if ($userRole->getUrid() == NULL) {
      $sql = 'INSERT INTO user_role (accid, appid, uid, rid) VALUES (?, ?, ?, ?)';
      $bindParams = [
        $userRole->getAccid(),
        $userRole->getAppid(),
        $userRole->getUid(),
        $userRole->getRid(),
      ];
    }
    else {
      $sql = 'UPDATE user_role SET (accid, appid, uid, rid) WHERE urid = ?';
      $bindParams = [
        $userRole->getAccid(),
        $userRole->getAppid(),
        $userRole->getUid(),
        $userRole->getRid(),
        $userRole->getUrid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete the user role.
   *
   * @param \Gaterdata\Db\UserRole $userRole
   *   UserRole object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function delete(UserRole $userRole) {
    $sql = 'DELETE FROM user_role WHERE urid = ?';
    $bindParams = [$userRole->getUrid()];
    return $this->saveDelete($sql, $bindParams);
  }
  /**
   * Find all user roles.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findAll() {
    $sql = 'SELECT * FROM user_role';
    $bindParams = [];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by user role ID.
   *
   * @param int $urid
   *   User role ID.
   *
   * @return \Gaterdata\Db\UserRole
   *   Mapped UserRole object.
   *
   * @throws ApiException
   */
  public function findByUrid($urid) {
    $sql = 'SELECT * FROM user_role WHERE urid = ?';
    $bindParams = [$urid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by application ID.
   *
   * @param int $appid
   *  Application ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByAppid($appid) {
    $sql = 'SELECT * FROM user_role WHERE appid = ?';
    $bindParams = [$appid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *  User ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM user_role WHERE uid = ?';
    $bindParams = [$uid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by role ID.
   *
   * @param int $rid
   *   Role ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM user_role WHERE rid = ?';
    $bindParams = [$rid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by role ID and ser ID.
   *
   * @param int $rid
   *   Role ID.
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByRidUid($rid, $uid) {
    $sql = 'SELECT * FROM user_role WHERE rid = ? AND uid = ?';
    $bindParams = [$rid, $uid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by application ID and user ID.
   *
   * @param int $appid
   *   Application ID.
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByAppidUid($appid, $uid) {
    $sql = 'SELECT * FROM user_role WHERE appid = ? AND uid = ?';
    $bindParams = [$appid, $uid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by Account ID, Application ID and User ID.
   *
   * @param int $accid
   *   Account ID.
   * @param int $appid
   *   Application ID.
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of mapped UserRole objects.
   *
   * @throws ApiException
   */
  public function findByAccidAppidUid($accid, $appid, $uid) {
    $sql = 'SELECT * FROM user_role WHERE accid = ? AND appid = ? AND uid = ?';
    $bindParams = [$accid, $appid, $uid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row to a UserRole object.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Gaterdata\Db\UserRole
   *   UserRole object.
   */
  protected function mapArray(array $row) {
    $userRole = new UserRole();

    $userRole->setUrid(!empty($row['urid']) ? $row['urid'] : NULL);
    $userRole->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $userRole->setAppid(!empty($row['appid']) ? $row['appid'] : NULL);
    $userRole->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $userRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);

    return $userRole;
  }

}
