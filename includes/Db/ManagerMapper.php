<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class ManagerMapper.
 *
 * @package Datagator\Db
 */
class ManagerMapper extends Mapper {

  /**
   * ManagerMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save aa manager.
   *
   * @param \Datagator\Db\Manager $manager
   *   Manager object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Manager $manager) {
    if ($manager->getMid() == NULL) {
      $sql = 'INSERT INTO manager (accid, uid) VALUES (?, ?)';
      $bindParams = [
        $manager->getAccid(),
        $manager->getUid(),
      ];
    }
    else {
      $sql = 'UPDATE manager SET accid = ?, uid = ? WHERE mid = ?';
      $bindParams = [
        $manager->getAccid(),
        $manager->getUid(),
        $manager->getMid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete a manager.
   *
   * @param \Datagator\Db\Manager $manager
   *   Manager object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Manager $manager) {

    $sql = 'DELETE FROM manager WHERE mid = ?';
    $bindParams = [$manager->getMid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find by manager ID.
   *
   * @param int $mid
   *   Manager ID.
   *
   * @return \Datagator\Db\Manager
   *   Manager object.
   *
   * @throws ApiException
   */
  public function findByMid($mid) {
    $sql = 'SELECT * FROM manager WHERE mid = ?';
    $bindParams = [$mid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by account ID.
   *
   * @param int $accid
   *   Account Id.
   *
   * @return array
   *   array of Manager objects.
   *
   * @throws ApiException
   */
  public function findByAccid($accid) {
    $sql = 'SELECT * FROM manager WHERE accid = ?';
    $bindParams = [$accid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *   User ID.
   * @param array|NULL $params
   *   parameters (optional)
   *     [
   *       'sort_by' => string,
   *       'direction' => string "asc"|"desc",
   *       'start' => int,
   *       'limit' => int,
   *     ]
   *
   * @return array
   *   array of Manager objects.
   *
   * @throws ApiException
   */
  public function findByUid($uid, array $params = NULL) {
    $sql = 'SELECT * FROM manager WHERE uid = ?';
    $bindParams = [$uid];
    return $this->fetchRows($sql, $bindParams, $params);
  }

  /**
   * Find by account ID & user ID.
   *
   * @param int $accid
   *   Account Id.
   * @param int $uid
   *   User ID.
   *
   * @return Manager
   *   Manager object.
   *
   * @throws ApiException
   */
  public function findByAccidUid($accid, $uid) {
    $sql = 'SELECT * FROM manager WHERE accid = ? AND uid = ?';
    $bindParams = [
      $accid,
      $uid,
    ];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Map a DB row into a Manager object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Manager
   *   Manager object.
   */
  protected function mapArray(array $row) {
    $manager = new Manager();

    $manager->setMid(!empty($row['mid']) ? $row['mid'] : NULL);
    $manager->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $manager->setUid(!empty($row['uid']) ? $row['uid'] : NULL);

    return $manager;
  }

}
