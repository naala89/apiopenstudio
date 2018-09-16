<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class ApplicationUserMapper.
 *
 * @package Datagator\Db
 */
class ApplicationUserMapper extends Mapper {

  /**
   * AccountOwnerMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an application user.
   *
   * @param \Datagator\Db\ApplicationUser $applicationUser
   *   ApplicationUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(ApplicationUser $applicationUser) {
    if ($applicationUser->getAuid() == NULL) {
      $sql = 'INSERT INTO application_user (appid, uid) VALUES (?, ?)';
      $bindParams = array(
        $applicationUser->getAppid(),
        $applicationUser->getUid(),
      );
    }
    else {
      $sql = 'UPDATE application_user SET appid = ?, uid = ? WHERE auid = ?';
      $bindParams = array(
        $applicationUser->getAppid(),
        $applicationUser->getUid(),
        $applicationUser->getAuid(),
      );
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an application user.
   *
   * @param \Datagator\Db\ApplicationUser $applicationUser
   *   ApplicationUser object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(ApplicationUser $applicationUser) {

    $sql = 'DELETE FROM application_user WHERE auid = ?';
    $bindParams = array($applicationUser->getAuid());
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find by application user ID.
   *
   * @param int $auid
   *   Application user Id.
   *
   * @return \Datagator\Db\ApplicationUser
   *   ApplicationUser object.
   *
   * @throws ApiException
   */
  public function findByAuid($auid) {
    $sql = 'SELECT * FROM application_user WHERE auid = ?';
    $bindParams = array($auid);
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by application ID.
   *
   * @param int $appid
   *   Application Id.
   *
   * @return array
   *   array of ApplicationUser objects.
   *
   * @throws ApiException
   */
  public function findByAppid($appid) {
    $sql = 'SELECT * FROM application_user WHERE appid = ?';
    $bindParams = array($appid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   array of ApplicationUser objects.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM application_user WHERE uid = ?';
    $bindParams = array($uid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row into an ApplicationUser object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\ApplicationUser
   *   ApplicationUser object.
   */
  protected function mapArray(array $row) {
    $applicationUser = new ApplicationUser();

    $applicationUser->setAuid(!empty($row['auid']) ? $row['auid'] : NULL);
    $applicationUser->setAppid(!empty($row['appid']) ? $row['appid'] : NULL);
    $applicationUser->setUid(!empty($row['uid']) ? $row['uid'] : NULL);

    return $applicationUser;
  }

}
