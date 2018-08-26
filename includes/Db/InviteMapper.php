<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;

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
      $sql = 'INSERT INTO invite (email, token) VALUES (?, ?)';
      $bindParams = array(
        $invite->getEmail(),
        $invite->getToken(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    else {
      $sql = 'UPDATE invite SET email = ?, token = ? WHERE id = ?';
      $bindParams = array(
        $invite->getEmail(),
        $invite->getToken(),
        $invite->getId(),
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
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

    $sql = 'DELETE FROM invite WHERE id = ?';
    $bindParams = array($invite->getId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * Find an invite by ID.
   *
   * @param int $invite
   *   Invite ID.
   *
   * @return \Datagator\Db\Invite
   *   Invite object.
   */
  public function findById($invite) {
    $sql = 'SELECT * FROM invite WHERE id = ?';
    $bindParams = array($invite);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * Find an invite by email.
   *
   * @param string $email
   *   Invite email.
   *
   * @return array
   *   Array of \Datagator\Db\Invite.
   */
  public function findByEmail($email) {
    $sql = 'SELECT * FROM invite WHERE email = ?';
    $bindParams = [$email];
    $recordSet = $this->db->Execute($sql, $bindParams);

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
   */
  public function findByToken($token) {
    $sql = 'SELECT * FROM invite WHERE token = ?';
    $bindParams = array($token);
    $row = $this->db->GetRow($sql, $bindParams);
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
