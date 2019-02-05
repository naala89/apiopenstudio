<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use ADOConnection;

/**
 * Class AdministratorMapper.
 *
 * @package Datagator\Db
 */
class AdministratorMapper extends Mapper {

  /**
   * AdministratorMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an administrator.
   *
   * @param \Datagator\Db\Administrator $administrator
   *   Administrator object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Administrator $administrator) {
    if ($administrator->getAid() == NULL) {
      $sql = 'INSERT INTO administrator (uid) VALUES (?)';
      $bindParams = [$administrator->getUid()];
    }
    else {
      $sql = 'UPDATE administrator SET uid = ? WHERE aid = ?';
      $bindParams = [
        $administrator->getUid(),
        $administrator->getAid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an administrator.
   *
   * @param \Datagator\Db\Administrator $administrator
   *   Administrator object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Administrator $administrator) {
    $sql = 'DELETE FROM administrator WHERE aid = ?';
    $bindParams = [$administrator->getAid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Fetch all administrators.
   *
   * @param array|NULL $params
   *   parameters (optional)
   *     [
   *       'keyword' => string,
   *       'sort_by' => string,
   *       'direction' => string "asc"|"desc",
   *       'start' => int,
   *       'limit' => int,
   *     ]
   *
   * @return array
   *   Array of Administrators.
   */
  public function findAll(array $params = NULL) {
    $sql = 'SELECT * FROM administrator';
    $bindParams = [];
    return $this->fetchRows($sql, $bindParams, $params);
  }

  /**
   * Find by administrator ID.
   *
   * @param int $aid
   *   Administrator ID.
   *
   * @return \Datagator\Db\Administrator
   *   Sysadmin object.
   *
   * @throws ApiException
   */
  public function findBySid($aid) {
    $sql = 'SELECT * FROM administrator WHERE aid = ?';
    $bindParams = [$aid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return \Datagator\Db\Administrator
   *   Sysadmin object.
   *
   * @throws ApiException
   */
  public function findByUid($uid) {
    $sql = 'SELECT * FROM administrator WHERE uid = ?';
    $bindParams = [$uid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Map a DB row into an Administrator object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Administrator
   *   Administrator object.
   */
  protected function mapArray(array $row) {
    $administrator = new Administrator();

    $administrator->setAid(!empty($row['aid']) ? $row['aid'] : NULL);
    $administrator->setUid(!empty($row['uid']) ? $row['uid'] : NULL);

    return $administrator;
  }

}
