<?php

namespace Gaterdata\Admin;

use Gaterdata\Db;
use Gaterdata\Core\ApiException;

/**
 * Class User.
 *
 * @package Gaterdata\Admin
 */
class Invite
{

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
    public function __construct(array $dbSettings)
    {
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
    public function getInvite()
    {
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
   * @throws \Gaterdata\Core\ApiException
   */
    public function create($accid, $email, $token)
    {
        $invite = new Db\Invite(
            null,
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
   * @throws \Gaterdata\Core\ApiException
   */
    public function deleteByEmail($email)
    {
        $inviteMapper = new Db\InviteMapper($this->db);
        $invites = $inviteMapper->findByEmail($email);
        foreach ($invites as $invite) {
            $inviteMapper->delete($invite);
        }
        return true;
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
   * @throws \Gaterdata\Core\ApiException
   */
    public function deleteByToken($token)
    {
        $inviteMapper = new Db\InviteMapper($this->db);
        $invite = $inviteMapper->findByToken($token);
        $inviteMapper->delete($invite);
        return true;
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
    public function findById($iid)
    {
        $inviteMapper = new Db\InviteMapper($this->db);
        $this->invite = $inviteMapper->findById($iid);
        return $this->getInvite();
    }

    /**
     * Find by email,
     * @param $email
     * @return array
     * @throws ApiException
     */
    public function findByEmail($email)
    {
        $inviteMapper = new Db\InviteMapper($this->db);
        $this->invite = $inviteMapper->findByEmail($email);
        return $this->getInvite();
    }

    /**
     * Find by token.
     * @param $token
     * @return array
     * @throws ApiException
     */
    public function findByToken($token)
    {
        $inviteMapper = new Db\InviteMapper($this->db);
        $this->invite = $inviteMapper->findByToken($token);
        return $this->getInvite();
    }
}
