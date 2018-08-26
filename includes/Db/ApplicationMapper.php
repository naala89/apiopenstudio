<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class ApplicationMapper.
 *
 * @package Datagator\Db
 */
class ApplicationMapper {

  protected $db;

  /**
   * ApplicationMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
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
    if ($application->getAppId() == NULL) {
      $sql = 'INSERT INTO application (accid, name) VALUES (?, ?)';
      $bindParams = array(
        $application->getAccId(),
        $application->getName(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE application SET accid = ?, name = ? WHERE appid = ?';
      $bindParams = array(
        $application->getAccId(),
        $application->getName(),
        $application->getAppId(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
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
    $bindParams = array($application->getAppId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find application by application ID.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return \Datagator\Db\Application
   *   Application object.
   */
  public function findByAppId($appId) {
    $sql = 'SELECT * FROM application WHERE appid = ?';
    $bindParams = array($appId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find application by application name.
   *
   * @param string $name
   *   Application name.
   *
   * @return \Datagator\Db\Application
   *   Application object.
   */
  public function findByName($name) {
    $sql = 'SELECT * FROM application WHERE name = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find applications by account ID.
   *
   * @param int $accId
   *   Account ID.
   *
   * @return \Datagator\Db\Application
   *   Application object
   */
  public function findByAccId($accId) {
    $sql = 'SELECT * FROM application WHERE accid = ?';
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
   * Map a DB row to this object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Application
   *   Application object
   */
  protected function mapArray(array $row) {
    $application = new Application();

    $application->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $application->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);
    $application->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $application;
  }

}