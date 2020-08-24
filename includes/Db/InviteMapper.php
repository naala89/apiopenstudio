<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use Gaterdata\Core\Utilities;

/**
 * Class InviteMapper.
 *
 * @package Gaterdata\Db
 */
class InviteMapper extends Mapper
{
    /**
     * Save an Invite.
     *
     * @param \Gaterdata\Db\Invite $invite
     *   Invite object.
     *
     * @return bool
     *   Success.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function save(Invite $invite)
    {
        if ($invite->getIid() == null) {
            $sql = 'INSERT INTO invite (created, email, token) VALUES (?, ?, ?)';
            $bindParams = [
                empty($invite->getCreated()) ? "now()" : $invite->getCreated(),
                $invite->getEmail(),
                $invite->getToken(),
            ];
        } else {
            $sql = 'UPDATE invite SET created = ?, email = ?, token = ? WHERE iid = ?';
            $bindParams = [
                empty($invite->getCreated()) ? "now()" : $invite->getCreated(),
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
     * @param Invite $invite
     *   Invite object.
     *
     * @return bool
     *   Success.
     *
     * @throws ApiException
     */
    public function delete(Invite $invite)
    {
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
     * @return Invite
     *   Invite object.
     *
     * @throws ApiException
     */
    public function findByIid($iid)
    {
        $sql = 'SELECT * FROM invite WHERE id = ?';
        $bindParams = [$iid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an invite by email.
     *
     * @param string $email
     *   Invite email
     * @return array
     *   Array of Invite.
     *
     * @throws ApiException
     */
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM invite WHERE email = ?';
        $bindParams = [$email];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an invite by token.
     *
     * @param string $token
     *   Invite token.
     *
     * @return Invite
     *   Invite object.
     *
     * @throws ApiException
     */
    public function findByToken($token)
    {
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
     * @return Invite
     *   Invite object.
     *
     * @throws ApiException
     */
    public function findByEmailToken($email, $token)
    {
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
     * @return Invite
     *   Invite object.
     */
    protected function mapArray(array $row)
    {
        $invite = new Invite();

        $invite->setIid(!empty($row['iid']) ? $row['iid'] : null);
        $invite->setCreated(!empty($row['created']) ? $row['created'] : null);
        $invite->setEmail(!empty($row['email']) ? $row['email'] : null);
        $invite->setToken(!empty($row['token']) ? $row['token'] : null);

        return $invite;
    }
}
