<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class AccountMapper.
 *
 * @package Datagator\Db
 */
class AccountMapper extends Mapper {

  /**
   * AccountMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an Account.
   *
   * @param \Datagator\Db\Account $account
   *   Account object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Account $account) {
    if ($account->getAccid() == NULL) {
      $sql = 'INSERT INTO account (name) VALUES (?)';
      $bindParams = [$account->getName()];
    }
    else {
      $sql = 'UPDATE account SET name = ? WHERE accid = ?';
      $bindParams = [
        $account->getName(),
        $account->getAccid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an account.
   *
   * @param \Datagator\Db\Account $account
   *   Account object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Account $account) {

    $sql = 'DELETE FROM account WHERE accid = ?';
    $bindParams = [$account->getAccid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find an accounts.
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
   *   array Account objects.
   *
   * @throws ApiException
   */
  public function findAll(array $params = NULL) {
    $sql = 'SELECT * FROM account';
    $bindParams = [];
    if (!empty($params)) {
      if (!empty($params['keyword'])) {
        $sql .= ' WHERE name like "%' . $params['keyword'] . '%"';
        unset($params['keyword']);
      }
    }
    return $this->fetchRows($sql, $bindParams, $params);
  }

  /**
   * Find an account by ID.
   *
   * @param int $accid
   *   Account Id.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   *
   * @throws ApiException
   */
  public function findByAccid($accid) {
    $sql = 'SELECT * FROM account WHERE accid = ?';
    $bindParams = [$accid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find an account by name.
   *
   * @param string $name
   *   Account name.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   *
   * @throws ApiException
   */
  public function findByName($name) {
    $sql = 'SELECT * FROM account WHERE name = ?';
    $bindParams = [$name];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Map a DB row into an Account object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  protected function mapArray(array $row) {
    $account = new Account();

    $account->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $account->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $account;
  }

}
