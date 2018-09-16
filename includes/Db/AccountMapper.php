<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

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
      $bindParams = array(
        $account->getName(),
      );
    }
    else {
      $sql = 'UPDATE account SET name = ? WHERE aid = ?';
      $bindParams = array(
        $account->getName(),
        $account->getAccid(),
      );
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
    $bindParams = array($account->getAccid());
    return $this->saveDelete($sql, $bindParams);
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
    $bindParams = array($accid);
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
    $bindParams = array($name);
    return $this->fetchRows($sql, $bindParams);
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
