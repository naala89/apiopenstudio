<?php

/**
 * Fetch and save account data.
 */

namespace Datagator\Db;

use Datagator\Core;
use \ADOConnection;

class AccountMapper
{
  protected $db;

  /**
   * AccountMapper constructor.
   *
   * @param ADOConnection $dbLayer
   */
  public function __construct(ADOConnection $dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\Account $account
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Account $account)
  {
    if ($account->getAccId() == NULL) {
      $sql = 'INSERT INTO account (name) VALUES (?)';
      $bindParams = array(
        $account->getName()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE account SET name = ? WHERE aid = ?';
      $bindParams = array(
        $account->getName(),
        $account->getAccId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param \Datagator\Db\Account $account
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Account $account)
  {

    $sql = 'DELETE FROM account WHERE accid = ?';
    $bindParams = array($account->getAccId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return true;
  }

  /**
   * @param $accId
   * @return \Datagator\Db\Account
   */
  public function findByAccId($accId)
  {
    $sql = 'SELECT * FROM account WHERE accid = ?';
    $bindParams = array($accId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $name
   * @return \Datagator\Db\Account
   */
  public function findByName($name)
  {
    $sql = 'SELECT * FROM account WHERE name = ?';
    $bindParams = array($name);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $accId
   * @param $uid
   * @return \Datagator\Db\Account
   */
  public function findByAccIdUid($accId, $uid)
  {
    $sql = 'SELECT * FROM account WHERE accid = ? AND uid = ?';
    $bindParams = array($accId, $uid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param array $row
   * @return \Datagator\Db\Account
   */
  protected function mapArray(array $row)
  {
    $account = new Account();

    $account->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);
    $account->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $account;
  }
}
