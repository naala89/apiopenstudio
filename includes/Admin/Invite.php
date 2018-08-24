<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Core\Utilities;
use Datagator\Core\Hash;

/**
 * Class User.
 *
 * @package Datagator\Admin
 */
class Invite {

  private $dbSettings;
  private $db;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptions = '';
    if (count($dbSettings['options']) > 0) {
      foreach ($dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($dbSettings['options']) > 0 ? '?' . implode('&', $dbSettings['options']) : '';
    $dsn = $dbSettings['driver'] . '://' .
      $dbSettings['username'] . ':' .
      $dbSettings['password'] . '@' .
      $dbSettings['host'] . '/' .
      $dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Create an invite.
   *
   * @param string $email
   *   Invite email.
   * @param string $token
   *   Invite token.
   *
   * @return bool|int
   *   False | invite ID.
   */
  public function create($email, $token) {
    $invite = new Db\Invite(
      $email,
      $token
    );

    $inviteMapper = new Db\InviteMapper($this->db);
    $result = $inviteMapper->save($invite);
    if (!$result) {
      return FALSE;
    }
    $invite = $inviteMapper->findByToken($token);
    return $invite->getId();
  }

  /**
   * Delete all invites by email.
   *
   * @param string $email
   *   Invite email.
   *
   * @return bool
   *   Success.
   */
  public function deleteByEmail($email) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $results = $inviteMapper->findByEmail($email);
    foreach ($results as $result) {
      $inviteMapper->delete($result);
    }
    return TRUE;
  }

  /**
   * Find by id.
   *
   * @param int $id
   *   Invite ID.
   *
   * @return array
   *   Invite.
   */
  public function findByAccount($id) {
    $inviteMapper = new Db\InviteMapper($this->db);
    return $inviteMapper->findById($id);
  }

  /**
   * Find by email,
   *
   * @param string $email
   *   Invite email.
   *
   * @return array
   *   Invite.
   */
  public function findByEmail($email) {
    $inviteMapper = new Db\InviteMapper($this->db);
    return $inviteMapper->findByEmail($email);
  }

  /**
   * Find by token.
   *
   * @param string $token
   *   Invite token.
   *
   * @return array
   *   Invite
   */
  public function findByToken($token) {
    $inviteMapper = new Db\InviteMapper($this->db);
    return $inviteMapper->findByToken($token);
  }

}
