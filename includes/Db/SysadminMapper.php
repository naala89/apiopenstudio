<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class SysadminMapper.
 *
 * @package Datagator\Db
 */
class SysadminMapper extends Mapper {

  /**
   * SysadminMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save a Sysadmin.
   *
   * @param \Datagator\Db\Sysadmin $sysadmin
   *   Sysadmin object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Sysadmin $sysadmin) {
    if ($sysadmin->getSid() == NULL) {
      $sql = 'INSERT INTO sysadmin (uid) VALUES (?)';
      $bindParams = [$sysadmin->getUid()];
    }
    else {
      $sql = 'UPDATE account SET uid = ? WHERE sid = ?';
      $bindParams = [
        $sysadmin->getUid(),
        $sysadmin->getSid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete a sysadmin.
   *
   * @param \Datagator\Db\Sysadmin $sysadmin
   *   Sysadmin object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Sysadmin $sysadmin) {
    $sql = 'DELETE FROM sysadmin WHERE sid = ?';
    $bindParams = [$sysadmin->getSid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find by sysadmin ID.
   *
   * @param int $sid
   *   Sysadmin ID.
   *
   * @return \Datagator\Db\Sysadmin
   *   Sysadmin object.
   *
   * @throws ApiException
   */
  public function findBySid($sid) {
    $sql = 'SELECT * FROM sysadmin WHERE sid = ?';
    $bindParams = [$sid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return \Datagator\Db\Sysadmin
   *   Sysadmin object.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM sysadmin WHERE uid = ?';
    $bindParams = [$uid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Map a DB row into an Account object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  protected function mapArray(array $row) {
    $sysadmin = new Sysadmin();

    $sysadmin->setSid(!empty($row['sid']) ? $row['sid'] : NULL);
    $sysadmin->setUid(!empty($row['uid']) ? $row['uid'] : NULL);

    return $account;
  }

}
