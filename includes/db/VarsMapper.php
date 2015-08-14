<?php

/**
 * Fetch and save vars data.
 */

namespace Datagator\Db;
use Datagator\Core;

class VarsMapper
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
   * @param \Datagator\Db\Vars $vars
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Vars $vars)
  {
    if ($vars->getId() == NULL) {
      $sql = 'INSERT INTO vars (`appid`, `name`, `val`) VALUES (?, ?, ?)';
      $bindParams = array(
        $vars->getAppId(),
        $vars->getName(),
        $vars->getval()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE vars SET `appid`=?, `name`=?, `val`=? WHERE `id` = ?';
      $bindParams = array(
        $vars->getAppId(),
        $vars->getName(),
        $vars->getVal(),
        $vars->getId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg());
    }
    return TRUE;
  }

  /**
   * @param \Datagator\Db\Vars $vars
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Vars $vars)
  {
    if ($vars->getId() === NULL) {
      throw new Core\ApiException('cannot delete var - empty ID');
    }
    $sql = 'DELETE FROM vars WHERE `id` = ?';
    $bindParams = array($vars->getId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg());
    }
    return TRUE;
  }

  /**
   * @param $id
   * @return \Datagator\Db\Vars
   */
  public function findById($id)
  {
    $sql = 'SELECT * FROM vars WHERE `id` = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $appId
   * @param $name
   * @return \Datagator\Db\Vars
   */
  public function findByAppIdName($appId, $name)
  {
    $sql = 'SELECT * FROM vars WHERE `appid` = ? AND `name` = ?';
    $bindParams = array($appId, $name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $appId
   * @return array
   */
  public function findByAppId($appId)
  {
    $sql = 'SELECT * FROM vars WHERE `appid` = ?';
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
   * @return \Datagator\Db\Vars
   */
  protected function mapArray(array $row)
  {
    $vars = new Vars();

    $vars->setId(!empty($row['id']) ? $row['id'] : NULL);
    $vars->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $vars->setName(!empty($row['name']) ? $row['name'] : NULL);
    $vars->setVal(!empty($row['val']) ? $row['val'] : NULL);

    return $vars;
  }
}
