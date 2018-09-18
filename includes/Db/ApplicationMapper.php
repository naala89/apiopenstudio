<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class ApplicationMapper.
 *
 * @package Datagator\Db
 */
class ApplicationMapper extends Mapper {

  /**
   * ApplicationMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an Application object.
   *
   * @param \Datagator\Db\Application $application
   *   The Applicationm object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Application $application) {
    if ($application->getAppid() == NULL) {
      $sql = 'INSERT INTO application (accid, name) VALUES (?, ?)';
      $bindParams = [
        $application->getAccid(),
        $application->getName(),
      ];
    }
    else {
      $sql = 'UPDATE application SET accid = ?, name = ? WHERE appid = ?';
      $bindParams = [
        $application->getAccid(),
        $application->getName(),
        $application->getAppid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an application.
   *
   * @param \Datagator\Db\Application $application
   *   Application object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Application $application) {
    $sql = 'DELETE FROM application WHERE appid = ?';
    $bindParams = [$application->getAppid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find applications.
   *
   * @return array
   *   Array of Application objects.
   *
   * @throws ApiException
   */
  public function findAll() {
    $sql = 'SELECT * FROM application';
    $bindParams = [];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find application by application ID.
   *
   * @param int $appid
   *   Application ID.
   *
   * @return \Datagator\Db\Application
   *   Application object.
   *
   * @throws ApiException
   */
  public function findByAppid($appid) {
    $sql = 'SELECT * FROM application WHERE appid = ?';
    $bindParams = [$appid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find application by account ID and application name.
   *
   * @param int $accid
   *   Account ID.
   * @param string $name
   *   Application name.
   *
   * @return \Datagator\Db\Application
   *   Application object.
   *
   * @throws ApiException
   */
  public function findByAccidName($accid, $name) {
    $sql = 'SELECT * FROM application WHERE accid = ? AND name = ?';
    $bindParams = [
      $accid,
      $name,
    ];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find applications by account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return array
   *   array of mapped Application objects.
   *
   * @throws ApiException
   */
  public function findByAccid($accid) {
    $sql = 'SELECT * FROM application WHERE accid = ?';
    $bindParams = [$accid];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row into an Application object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Application
   *   Application object
   */
  protected function mapArray(array $row) {
    $application = new Application();

    $application->setAppid(!empty($row['appid']) ? $row['appid'] : NULL);
    $application->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $application->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $application;
  }

}
