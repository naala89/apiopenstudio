<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use ADOConnection;

/**
 * Class ApplicationUserRoleMapper.
 *
 * @package Gaterdata\Db
 */
class ApplicationUserRoleMapper extends Mapper {

  /**
   * UserAccountRoleMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save the application user role.
   *
   * @param \Gaterdata\Db\ApplicationUserRole $applicationUserRole
   *   ApplicationUserRole object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function save(ApplicationUserRole $applicationUserRole) {
    if ($applicationUserRole->getAurid() == NULL) {
      $sql = 'INSERT INTO application_user_role (appid, uid, rid) VALUES (?, ?, ?)';
      $bindParams = [
        $applicationUserRole->getAppid(),
        $applicationUserRole->getUid(),
        $applicationUserRole->getRid(),
      ];
    }
    else {
      $sql = 'UPDATE application_user_role SET (appid, uid, rid) WHERE aurid = ?';
      $bindParams = [
        $applicationUserRole->getAppid(),
        $applicationUserRole->getUid(),
        $applicationUserRole->getRid(),
        $applicationUserRole->getAurid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete the application user role.
   *
   * @param \Gaterdata\Db\ApplicationUserRole $applicationUserRole
   *   ApplicationUserRole object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
   */
  public function delete(ApplicationUserRole $applicationUserRole) {
    $sql = 'DELETE FROM application_user_role WHERE aurid = ?';
    $bindParams = [$applicationUserRole->getAurid()];
    return $this->saveDelete($sql, $bindParams);
  }
  /**
   * Find by application user roles.
   *
   * @return array
   *   Arrap of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findAll() {
    $sql = 'SELECT * FROM application_user_role';
    $bindParams = [];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by application user role ID.
   *
   * @param int $aurid
   *   Application user role ID.
   *
   * @return \Gaterdata\Db\ApplicationUserRole
   *   Mapped ApplicationUserRole object.
   *
   * @throws ApiException
   */
  public function findByAurid($aurid) {
    $sql = 'SELECT * FROM application_user_role WHERE aurid = ?';
    $bindParams = [$aurid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by application ID..
   *
   * @param int $appid
   *  Application ID.
   *
   * @return array
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByAppid($appid) {
    $sql = 'SELECT * FROM application_user_role WHERE appid = ?';
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
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM application_user_role WHERE uid = ?';
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
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM application_user_role WHERE rid = ?';
    $bindParams = [$rid];
    return $this->fetchRows($sql, $bindParams);
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
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByAppidUid($appid, $uid) {
    $sql = 'SELECT * FROM application_user_role WHERE appid = ? AND uid = ?';
    $bindParams = [$appid, $uid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row to a ApplicationUserRole object.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Gaterdata\Db\ApplicationUserRole
   *   ApplicationUserRole object.
   */
  protected function mapArray(array $row) {
    $applicationUserRole = new ApplicationUserRole();

    $applicationUserRole->setAurid(!empty($row['aurid']) ? $row['aurid'] : NULL);
    $applicationUserRole->setAppid(!empty($row['appid']) ? $row['appid'] : NULL);
    $applicationUserRole->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $applicationUserRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);

    return $applicationUserRole;
  }

}
