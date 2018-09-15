<?php

namespace Datagator\Admin;

use Datagator\Db;
use Datagator\Core\ApiException;

/**
 * Class User.
 *
 * @package Datagator\Admin
 */
class Invite {

  /**
   * @var array
   */
  private $dbSettings;
  /**
   * @var \ADOConnection
   */
  private $db;
  /**
   * @var  Db\Invite
   */
  private $invite;

  /**
   * Invite constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   *
   * @throws ApiException
   */
  public function __construct(array $dbSettings) {
    $this->dbSettings = $dbSettings;

    $dsnOptionsArr = [];
    foreach ($dbSettings['options'] as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('Failed to connect to the database.');
    }
  }

  /**
   * Get the Invite.
   *
   * @return array
   *   Invite.
   */
  public function getInvite() {
    return $this->invite->dump();
  }

  /**
   * Create an invite.
   *
   * @param int $accid
   *   Account ID.
   * @param string $email
   *   Invite email.
   * @param string $token
   *   Invite token.
   *
   * @return bool|int
   *   Mapped invite object.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function create($accid, $email, $token) {
    $invite = new Db\Invite(
      NULL,
      $accid,
      $email,
      $token
    );

    $inviteMapper = new Db\InviteMapper($this->db);
    $inviteMapper->save($invite);
    $this->invite = $inviteMapper->findByToken($token);
    return $this->getInvite();
  }

  /**
   * Delete all invites by email.
   *
   * @param string $email
   *   Invite email.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function deleteByEmail($email) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $invites = $inviteMapper->findByEmail($email);
    foreach ($invites as $invite) {
      $inviteMapper->delete($invite);
    }
    return TRUE;
  }

  /**
   * Delete all invites by token.
   *
   * @param string $token
   *   Invite token.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function deleteByToken($token) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $invite = $inviteMapper->findByToken($token);
    $inviteMapper->delete($invite);
    return TRUE;
  }

  /**
   * Find by iid.
   *
   * @param int $iid
   *   Invite ID.
   *
   * @return array
   *   Invite.
   */
  public function findById($iid) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $this->invite = $inviteMapper->findById($iid);
    return $this->getInvite();
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
    $this->invite = $inviteMapper->findByEmail($email);
    return $this->getInvite();
  }

  /**
   * Find by token.
   *
   * @param string $token
   *   Invite token.
   *
   * @return array
   *   Invite.
   */
  public function findByToken($token) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $this->invite = $inviteMapper->findByToken($token);
    return $this->getInvite();
  }

  /**
   * Find by email and token.
   *
   * @param string $email
   *   Invite email.
   *
   * @param string $token
   *   Invite token.
   *
   * @return array
   *   Invite.
   */
  public function findByEmailToken($email, $token) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $this->invite = $inviteMapper->findByEmailToken($email, $token);
    return $this->getInvite();
  }

}
