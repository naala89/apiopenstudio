<?php

/**
 * Fetch and save application data.
 */

namespace Datagator\Db;
use Datagator\Core;

class ApplicationMapper
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
   * @param \Datagator\Db\Application $application
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Application $application)
  {
    if ($application->getAppId() == NULL) {
      $sql = 'INSERT INTO application (accid, name) VALUES (?, ?)';
      $bindParams = array(
        $application->getAccId(),
        $application->getName()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE application SET accid = ?, name = ? WHERE appid = ?';
      $bindParams = array(
        $application->getAccId(),
        $application->getName(),
        $application->getAppId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param $accId
   * @param $name
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function deleteByAccIdName($accId, $name)
  {
    $sql = 'DELETE FROM application WHERE accid = ? AND name = ?';
    $bindParams = array($accId, $name);
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return true;
  }

  /**
   * @param $appId
   * @return \Datagator\Db\Application
   */
  public function findByAppId($appId)
  {
    $sql = 'SELECT * FROM application WHERE appid = ?';
    $bindParams = array($appId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $accId
   * @return \Datagator\Db\Application
   */
  public function findByAccId($accId)
  {
    $sql = 'SELECT * FROM application WHERE accid = ?';
    $bindParams = array($accId);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $name
   * @return \Datagator\Db\Application
   */
  public function findByName($name)
  {
    $sql = 'SELECT * FROM application WHERE name = ?';
    $bindParams = array($name);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $accId
   * @param $name
   * @return \Datagator\Db\Application
   */
  public function findByAccIdName($accId, $name)
  {
    $sql = 'SELECT * FROM application WHERE accid = ? AND name = ?';
    $bindParams = array($accId, $name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param array $row
   * @return \Datagator\Db\Application
   */
  protected function mapArray(array $row)
  {
    $application = new Application();

    $application->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $application->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);
    $application->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $application;
  }
}
