<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

/**
 * Class InviteMapper.
 *
 * @package Datagator\Db
 */
class InviteMapper extends Mapper {

  /**
   * InviteMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    parent::__construct($dbLayer);
  }

  /**
   * Save an Invite.
   *
   * @param \Datagator\Db\Invite $invite
   *   Invite object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(Invite $invite) {
    if ($invite->getIid() == NULL) {
      $sql = 'INSERT INTO invite (accid, email, token) VALUES (?, ?, ?)';
      $bindParams = [
        $invite->getAccId(),
        $invite->getEmail(),
        $invite->getToken(),
      ];
    }
    else {
      $sql = 'UPDATE invite SET accid= ?, email = ?, token = ? WHERE iid = ?';
      $bindParams = [
        $invite->getAccId(),
        $invite->getEmail(),
        $invite->getToken(),
        $invite->getIid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an invite.
   *
   * @param \Datagator\Db\Invite $invite
   *   Invite object.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Invite $invite) {
    $sql = 'DELETE FROM invite WHERE iid = ?';
    $bindParams = [$invite->getIid()];
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Find an invite by ID.
   *
   * @param int $iid
   *   Invite ID.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   *
   * @throws ApiException
   */
  public function findByIid($iid) {
    $sql = 'SELECT * FROM invite WHERE id = ?';
    $bindParams = [$iid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find an invite by email.
   *
   * @param string $email
   *   Invite emaill
   * @return array
   *   Array of \Datagator\Db\Invite.
   *
   * @throws ApiException
   */
  public function findByEmail($email) {
    $sql = 'SELECT * FROM invite WHERE email = ?';
    $bindParams = [$email];
    return $this->fetchRows($sql, $bindParams);
  }

  /**
   * Find an invite by token.
   *
   * @param string $token
   *   Invite token.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   *
   * @throws ApiException
   */
  public function findByToken($token) {
    $sql = 'SELECT * FROM invite WHERE token = ?';
    $bindParams = [$token];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find an invite by email and token.
   *
   * @param string $email
   *   Invite email.
   * @param string $token
   *   Invite token.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   *
   * @throws ApiException
   */
  public function findByEmailToken($email, $token) {
    $sql = 'SELECT * FROM invite WHERE email = ? AND token = ?';
    $bindParams = [$email, $token];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Map a DB row into an Invite object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   */
  protected function mapArray(array $row) {
    $invite = new Invite();

    $invite->setIid(!empty($row['iid']) ? $row['iid'] : NULL);
    $invite->setAccId(!empty($row['accid']) ? $row['accid'] : NULL);
    $invite->setEmail(!empty($row['email']) ? $row['email'] : NULL);
    $invite->setToken(!empty($row['token']) ? $row['token'] : NULL);

    return $invite;
  }

}
