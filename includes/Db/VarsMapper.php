<?php

namespace Datagator\Db;

use Cascade\Cascade;
use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class VarsMapper.
 *
 * @package Datagator\Db
 */
class VarsMapper {

  protected $db;

  /**
   * VarsMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save the var.
   *
   * @param \Datagator\Db\Vars $vars
   *   Vars object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Vars $vars) {
    if ($vars->getId() == NULL) {
      $sql = 'INSERT INTO vars (appid, name, val) VALUES (?, ?, ?)';
      $bindParams = array(
        $vars->getAppId(),
        $vars->getName(),
        $vars->getval(),
      );
    }
    else {
      $sql = 'UPDATE vars SET appid=?, name=?, val=? WHERE id = ?';
      $bindParams = array(
        $vars->getAppId(),
        $vars->getName(),
        $vars->getVal(),
        $vars->getId(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Delete the vars.
   *
   * @param \Datagator\Db\Vars $vars
   *   Vars object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Vars $vars) {
    if ($vars->getId() === NULL) {
      throw new ApiException('cannot delete var - empty ID', 2);
    }
    $sql = 'DELETE FROM vars WHERE id = ?';
    $bindParams = array($vars->getId());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find a var by its ID.
   *
   * @param int $id
   *   Var ID.
   *
   * @return \Datagator\Db\Vars
   *   Vars object.
   *
   * @throws ApiException
   */
  public function findById($id) {
    $sql = 'SELECT * FROM vars WHERE id = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find a var by application ID and var name.
   *
   * @param int $appId
   *   Application ID.
   * @param string $name
   *   Var name.
   *
   * @return \Datagator\Db\Vars
   *   Vars object.
   *
   * @throws ApiException
   */
  public function findByAppIdName($appId, $name) {
    $sql = 'SELECT * FROM vars WHERE appid = ? AND name = ?';
    $bindParams = array($appId, $name);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find the vars belonging to an application.
   *
   * @param int $appId
   *   Application ID.
   *
   * @return array
   *   Array of Vars objects.
   *
   * @throws ApiException
   */
  public function findByAppId($appId) {
    $sql = 'SELECT * FROM vars WHERE appid = ?';
    $bindParams = array($appId);

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Map a results row to attributes.
   *
   * @param array $row
   *   DB results row.
   *
   * @return \Datagator\Db\Vars
   *   Vars object.
   */
  protected function mapArray(array $row) {
    $vars = new Vars();

    $vars->setId(!empty($row['id']) ? $row['id'] : NULL);
    $vars->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $vars->setName(!empty($row['name']) ? $row['name'] : NULL);
    $vars->setVal(!empty($row['val']) ? $row['val'] : NULL);

    return $vars;
  }

}
