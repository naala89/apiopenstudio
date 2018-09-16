<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class AccountOwnerMapper.
 *
 * @package Datagator\Db
 */
class AccountOwnerMapper extends Mapper {

  /**
   * AccountOwnerMapper constructor.
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
   * @param \Datagator\Db\AccountOwner $accountOwner
   *   AccountOwner object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(AccountOwner $accountOwner) {
    if ($accountOwner->getAoid() == NULL) {
      $sql = 'INSERT INTO account (accid, uid) VALUES (?, ?)';
      $bindParams = array(
        $accountOwner->getAccid(),
        $accountOwner->getUid(),
      );
    }
    else {
      $sql = 'UPDATE account SET accid = ?, uid = ? WHERE aoid = ?';
      $bindParams = array(
        $accountOwner->getAccid(),
        $accountOwner->getUid(),
        $accountOwner->getAoid(),
      );
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an account owner.
   *
   * @param \Datagator\Db\AccountOwner $accountOwner
   *   AccountOwner object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(AccountOwner $accountOwner) {

    $sql = 'DELETE FROM account_owner WHERE aoid = ?';
    $bindParams = array($accountOwner->getAoid());
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find by account owner ID.
   *
   * @param int $aoid
   *   Account owner Id.
   *
   * @return \Datagator\Db\Account
   *   Account object.
   *
   * @throws ApiException
   */
  public function findByAoid($aoid) {
    $sql = 'SELECT * FROM account WHERE aoid = ?';
    $bindParams = array($aoid);
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find by account ID.
   *
   * @param int $accid
   *   Account Id.
   *
   * @return array
   *   array of AccountOwner objects.
   *
   * @throws ApiException
   */
  public function findByAccid($accid) {
    $sql = 'SELECT * FROM account_owner WHERE accid = ?';
    $bindParams = array($accid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   array of AccountOwner objects.
   *
   * @throws ApiException
   */
  public function findByName($uid) {
    $sql = 'SELECT * FROM account WHERE uid = ?';
    $bindParams = array($uid);
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Map a DB row into an AccountOwner object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\AccountOwner
   *   AccountOwner object.
   */
  protected function mapArray(array $row) {
    $accountOwner = new AccountOwner();

    $accountOwner->setAoid(!empty($row['aoid']) ? $row['aoid'] : NULL);
    $accountOwner->setAccid(!empty($row['accid']) ? $row['accid'] : NULL);
    $accountOwner->setUid(!empty($row['uid']) ? $row['uid'] : NULL);

    return $accountOwner;
  }

}
