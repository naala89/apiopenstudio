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
class VarsMapper extends Mapper {

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
      $bindParams = [
        $vars->getAppId(),
        $vars->getName(),
        $vars->getval(),
      ];
    }
    else {
      $sql = 'UPDATE vars SET appid=?, name=?, val=? WHERE id = ?';
      $bindParams = [
        $vars->getAppId(),
        $vars->getName(),
        $vars->getVal(),
        $vars->getId(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
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
    $bindParams = [$vars->getId()];
    return $this->saveDelete($sql, $bindParams);
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
    $bindParams = [$id];
    return $this->fetchRow($sql, $bindParams);
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
    $bindParams = [$appId, $name];
    return $this->fetchRow($sql, $bindParams);
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
    $bindParams = [$appId];
    return $this->fetchRows($sql, $bindParams);
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
