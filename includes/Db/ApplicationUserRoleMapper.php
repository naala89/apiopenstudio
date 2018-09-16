<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class ApplicationUserRoleMapper.
 *
 * @package Datagator\Db
 */
class ApplicationUserRoleMapper extends Mapper {

  /**
   * UserAccountRoleMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save the application user role.
   *
   * @param \Datagator\Db\ApplicationUserRole $applicationUserRole
   *   ApplicationUserRole object.
   *
   * @return bool
   *   Result of the save.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(ApplicationUserRole $applicationUserRole) {
    if ($applicationUserRole->getAurid() == NULL) {
      $sql = 'INSERT INTO application_user_role (auid, rid) VALUES (?, ?)';
      $bindParams = array(
        $applicationUserRole->getAuid(),
        $applicationUserRole->getRid(),
      );
    }
    else {
      $sql = 'UPDATE application_user_role SET (auid, rid) WHERE aurid = ?';
      $bindParams = array(
        $applicationUserRole->getAuid(),
        $applicationUserRole->getRid(),
        $applicationUserRole->getAurid(),
      );
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete the application user role.
   *
   * @param \Datagator\Db\ApplicationUserRole $applicationUserRole
   *   ApplicationUserRole object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(ApplicationUserRole $applicationUserRole) {
    $sql = 'DELETE FROM application_user_role WHERE aurid = ?';
    $bindParams = array($applicationUserRole->getAurid());
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find by aurid.
   *
   * @param int $aurid
   *   Application user role ID.
   *
   * @return \Datagator\Db\ApplicationUserRole
   *   Mapped ApplicationUserRole object.
   *
   * @throws ApiException
   */
  public function findByAurid($aurid) {
    $sql = 'SELECT * FROM application_user_role WHERE aurid = ?';
    $bindParams = array($aurid);
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by auid.
   *
   * @param int $auid
   *  Application user ID.
   *
   * @return array
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByAuid($auid) {
    $sql = 'SELECT * FROM application_user_role WHERE auid = ?';
    $bindParams = array($auid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by rid.
   *
   * @param int $rid
   *   Role ID.
   *
   * @return array
   *   Array of mapped ApplicationUserRole objects.
   *
   * @throws ApiException
   */
  public function findByRid($rid) {
    $sql = 'SELECT * FROM application_user_role WHERE rid = ?';
    $bindParams = array($rid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row to a ApplicationUserRole object.
   *
   * @param array $row
   *   DB Row.
   *
   * @return \Datagator\Db\ApplicationUserRole
   *   ApplicationUserRole object.
   */
  protected function mapArray(array $row) {
    $applicationUserRole = new ApplicationUserRole();

    $applicationUserRole->setAurid(!empty($row['aurid']) ? $row['aurid'] : NULL);
    $applicationUserRole->setAuid(!empty($row['auid']) ? $row['auid'] : NULL);
    $applicationUserRole->setRid(!empty($row['rid']) ? $row['rid'] : NULL);

    return $applicationUserRole;
  }

}
