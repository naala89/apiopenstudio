<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class AccountMapper.
 *
 * @package Datagator\Db
 */
class AccountMapper {

  protected $db;

  /**
   * AccountMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
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
    if ($account->getAccId() == NULL) {
      $sql = 'INSERT INTO account (name) VALUES (?)';
      $bindParams = array(
        $account->getName(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE account SET name = ? WHERE aid = ?';
      $bindParams = array(
        $account->getName(),
        $account->getAccId(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
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
    $bindParams = array($account->getAccId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find an account by ID.
   *
   * @param int $accId
   *   Account Id.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  public function findByAccId($accId) {
    $sql = 'SELECT * FROM account WHERE accid = ?';
    $bindParams = array($accId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find an account by name.
   *
   * @param string $name
   *   Account name.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  public function findByName($name) {
    $sql = 'SELECT * FROM account WHERE name = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find an account by user ID and Account ID.
   *
   * @param int $accId
   *   Account ID.
   * @param int $uid
   *   User ID.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  public function findByAccIdUid($accId, $uid) {
    $sql = 'SELECT * FROM account WHERE accid = ? AND uid = ?';
    $bindParams = array($accId, $uid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Map a DB row into attributes.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   */
  protected function mapArray(array $row) {
    $account = new Account();

    $account->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);
    $account->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $account;
  }

}
