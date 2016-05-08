<?php

/**
 * Fetch and save account data.
 */

namespace Datagator\Db;
use Datagator\Core;

class AccountMapper
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
   * @param \Datagator\Db\Account $account
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Account $account)
  {
    if ($account->getAccId() == NULL) {
      $sql = 'INSERT INTO account (uid, name) VALUES (?, ?)';
      $bindParams = array(
        $account->getUid(),
        $account->getName()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE account SET uid = ?, name = ? WHERE accid = ?';
      $bindParams = array(
        $account->getUid(),
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
   * @param $uid
   * @param $name
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function deleteByUidName($uid, $name)
  {
    $sql = 'DELETE FROM account WHERE uid = ? AND name = ?';
    $bindParams = array($uid, $name);
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return true;
  }

  /**
   * @param $accId
   * @return \Datagator\Db\ExternalUser
   */
  public function findByAccId($accId)
  {
    $sql = 'SELECT * FROM account WHERE accid = ?';
    $bindParams = array($accId);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $uid
   * @return array
   */
  public function findByUid($uid)
  {
    $sql = 'SELECT * FROM account WHERE uid = ?';
    $bindParams = array($uid);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $email
   * @return array
   */
  public function findByEmail($email)
  {
    $sql = 'SELECT a.* FROM account a INNER JOIN user u ON a.uid = u.uid WHERE u.email = ?';
    $bindParams = array($email);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $name
   * @return array
   */
  public function findByName($name)
  {
    $sql = 'SELECT * FROM account WHERE name = ?';
    $bindParams = array($name);
    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries   = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
    }

    return $entries;
  }

  /**
   * @param $uid
   * @param $name
   * @return \Datagator\Db\Account
   */
  public function findByUidName($uid, $name)
  {
    $sql = 'SELECT * FROM account WHERE uid = ? AND name = ?';
    $bindParams = array($uid, $name);
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
    $account->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $account->setName(!empty($row['name']) ? $row['name'] : NULL);

    return $account;
  }
}
