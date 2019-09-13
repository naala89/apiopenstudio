<?php

namespace Gaterdata\Admin;

use Gaterdata\Db;
use Gaterdata\Core\ApiException;

/**
 * Class Manager.
 *
 * @package Gaterdata\Manager
 */
class Manager {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var \Gaterdata\Db\Manager
   */
  private $manager;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   *
   * @throws ApiException
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptionsArr = [];
    foreach ($dbSettings['options'] as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('Failed to connect to the database.');
    }
  }

  /**
   * Get the manager.
   *
   * @return array
   *   Manager.
   */
  public function getManager() {
    return $this->manager->dump();
  }

  /**
   * Create a manager.
   *
   * @param int $accid
   *   Account ID.
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Manager.
   */
  public function create($accid = NULL, $uid = NULL) {
    $manager = new Db\Manager(
      NULL,
      $accid,
      $uid
    );
    $managerMapper = new Db\ManagerMapper($this->db);
    $managerMapper->save($manager);
    $this->manager = $managerMapper->findByAccidUid($accid, $uid);
    return $this->manager->dump();
  }

  /**
   * Delete a manager.
   *
   * @return bool
   *   Success.
   */
  public function delete() {
    $managerMapper = new Db\ManagerMapper($this->db);
    return $managerMapper->delete($this->manager);
  }

  /**
   * Find all managers.
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
   *   array of managers.
   */
  public function findAll(array $params = NULL) {
    $managerMapper = new Db\ManagerMapper($this->db);
    $managers = $managerMapper->findAll($params);
    $result = [];
    foreach ($administrators as $administrator) {
      $result[] = $administrator->dump();
    }
    return $result;
  }

  /**
   * Find by manager ID.
   *
   * @param int $mid
   *   Manager ID.
   *
   * @return array
   *   Manager.
   */
  public function findByManagerId($mid) {
    $managerMapper = new Db\ManagerMapper($this->db);
    $this->manager = $managerMapper->findByMid($mid);
    return $this->manager->dump();
  }

  /**
   * Find by account ID.
   *
   * @param int $accid
   *   Account ID.
   *
   * @return array
   *   Array of managers, indexed by manager ID.
   */
  public function findByAccountId($accid) {
    $managerMapper = new Db\ManagerMapper($this->db);
    $managers = $managerMapper->findByAccid($accid);
    $result = [];
    foreach ($managers as $manager) {
      $result[$manager->getMid()] = $manager->dump();
    }
    return $result;
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
   *   Array of managers, indexed by manager ID.
   */
  public function findByUserId($uid, array $params = NULL) {
    $managerMapper = new Db\ManagerMapper($this->db);
    $managers = $managerMapper->findByUid($uid, $params);
    $result = [];
    foreach ($managers as $manager) {
      $result[$manager->getMid()] = $manager->dump();
    }
    return $result;
  }

  /**
   * Add a user as owner.
   *
   * @param int $uid
   *   User ID.
   *
   * @return bool
   *   Success.
   */
  public function addOwner($uid) {
    $accountOwner = new Db\AccountOwner(
      NULL,
      $this->account->getAccid(),
      $uid
    );
    $accountOwnerMapper = new Db\AccountOwnerMapper($this->db);
    return $accountOwnerMapper->save($accountOwner);
  }

}
