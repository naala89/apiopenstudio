<?php

namespace Gaterdata\Db;

/**
 * Class AccountMapper.
 *
 * @package Gaterdata\Db
 */
class AccountMapper extends Mapper {

  /**
   * Save an Account.
   *
   * @param \Gaterdata\Db\Account $account
   *   Account object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
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
   * @param \Gaterdata\Db\Account $account
   *   Account object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Gaterdata\Core\ApiException
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
   *   @see Gaterdata\Db\Mapper.
   *
   * @return array
   *   array Account objects.
   *
   * @throws ApiException
   */
  public function findAll($params = []) {
    $sql = 'SELECT * FROM account';
    return $this->fetchRows($sql, [], $params);
  }

  /**
   * Find an account by ID.
   *
   * @param int $accid
   *   Account Id.
   *
   * @return \Gaterdata\Db\Account
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
   * Find accounts by IDs.
   *
   * @param Array $accids
   *   Account Ids.
   *
   * @return Array
   *   Array of Account objects.
   *
   * @throws ApiException
   */
  public function findByAccids(array $accids) {
    $inAccid = [];
    foreach ($accids as $accid) {
      $inAccid[] = '?';
    }
    $sql = 'SELECT * FROM account';
    if (!empty($inAccid)) {
      $sql .= ' WHERE accid IN (' . implode(', ', $inAccid) . ')';
    }
    return $this->fetchRow($sql, $accids);
  }

  /**
   * Find an account by name.
   *
   * @param string $name
   *   Account name.
   *
   * @return \Gaterdata\Db\Account
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
   * Find an accounts by names.
   *
   * @param array $names
   *   Account names.
   *
   * @return \Gaterdata\Db\Account
   *   Account object.
   *
   * @throws ApiException
   */
  public function findByNames(array $names = []) {
    $arr = [];
    foreach ($names as $name) {
      $arr[] = '?';
    }
    $sql = 'SELECT * FROM account WHERE name IN (' . implode(', ', $arr) . ')';
    $bindParams = $names;
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row into an Account object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Gaterdata\Db\Account
   *   Account object.
   */
  protected function mapArray(array $row) {
    $account = new Account();

    $account->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $account->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $account;
  }

}
