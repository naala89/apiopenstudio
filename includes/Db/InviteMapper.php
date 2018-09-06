<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use Cascade\Cascade;

/**
 * Class InviteMapper.
 *
 * @package Datagator\Db
 */
class InviteMapper {

  protected $db;

  /**
   * InviteMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
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
    if ($invite->getId() == NULL) {
      $sql = 'INSERT INTO invite (accid, email, token) VALUES (?, ?, ?)';
      $bindParams = array(
        $invite->getAccId(),
        $invite->getEmail(),
        $invite->getToken(),
      );
    }
    else {
      $sql = 'UPDATE invite SET accid= ?, email = ?, token = ? WHERE iid = ?';
      $bindParams = array(
        $invite->getAccId(),
        $invite->getEmail(),
        $invite->getToken(),
        $invite->getIid(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg();
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
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
    $bindParams = array($invite->getIid());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg();
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find an invite by ID.
   *
   * @param int $invite
   *   Invite ID.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   *
   * @throws ApiException
   */
  public function findById($invite) {
    $sql = 'SELECT * FROM invite WHERE id = ?';
    $bindParams = array($invite);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg();
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
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
    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg();
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = [];
    while ($row = $recordSet->fetchRow()) {
      $entries[] = $this->mapArray($row);
    }

    return $entries;
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
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg();
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
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
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg();
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
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

    $invite->setId(!empty($row['id']) ? $row['id'] : NULL);
    $invite->setEmail(!empty($row['email']) ? $row['email'] : NULL);
    $invite->setToken(!empty($row['token']) ? $row['token'] : NULL);

    return $invite;
  }

}
