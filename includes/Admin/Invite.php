<?php

namespace Datagator\Admin;

use Datagator\Db;
use Monolog\Logger;

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
   * @var \Monolog\Logger
   */
  private $logger;

  /**
   * User constructor.
   *
   * @param array $dbSettings
   *   Database settings.
   * @param \Monolog\Logger $logger
   *   Logger.
   */
  public function __construct(array $dbSettings, Logger $logger) {
    $this->dbSettings = $dbSettings;
    $this->logger = $logger;

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
   *
   * @throws \Datagator\Core\ApiException
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
   *
   * @throws \Datagator\Core\ApiException
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
    $result = $inviteMapper->findByToken($token);
    $inviteMapper->delete($result);
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
  public function findById($id) {
    $inviteMapper = new Db\InviteMapper($this->db);
    $invite = $inviteMapper->findById($id);
    return $invite->dump();
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
    $invite = $inviteMapper->findByEmail($email);
    return $invite->dump();
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
    $invite = $inviteMapper->findByToken($token);
    return $invite->dump();
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
    $invite = $inviteMapper->findByEmailToken($email, $token);
    return $invite->dump();
  }

}
