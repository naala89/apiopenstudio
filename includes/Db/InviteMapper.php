<?php

/**
 * Class InviteMapper.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\ApiException;

/**
 * Class InviteMapper.
 *
 * Mapper class for DB calls used for the invite table.
 */
class InviteMapper extends Mapper
{
    /**
     * Save an Invite.
     *
     * @param Invite $invite Invite object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(Invite $invite): bool
    {
        if ($invite->getIid() == null) {
            $sql = 'INSERT INTO invite (created, email, token) VALUES (NOW(), ?, ?)';
            $bindParams = [
                $invite->getEmail(),
                $invite->getToken(),
            ];
        } else {
            $sql = 'UPDATE invite SET created = NOW(), email = ?, token = ? WHERE iid = ?';
            $bindParams = [
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
     * @param Invite $invite Invite object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(Invite $invite): bool
    {
        $sql = 'DELETE FROM invite WHERE iid = ?';
        $bindParams = [$invite->getIid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find an invite by ID.
     *
     * @param array $params Filter params.
     *
     * @return array Array of invites.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params): array
    {
        $sql = 'SELECT * FROM invite';
        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Find an invite by ID.
     *
     * @param integer $iid Invite ID.
     *
     * @return Invite Invite object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByIid(int $iid): Invite
    {
        $sql = 'SELECT * FROM invite WHERE iid = ?';
        $bindParams = [$iid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an invite by email.
     *
     * @param string $email Invite email.
     *
     * @return Invite Array of Invite.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByEmail(string $email): Invite
    {
        $sql = 'SELECT * FROM invite WHERE email = ?';
        $bindParams = [$email];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an invite by token.
     *
     * @param string $token Invite token.
     *
     * @return Invite Invite object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByToken(string $token): Invite
    {
        $sql = 'SELECT * FROM invite WHERE token = ?';
        $bindParams = [$token];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an invite by email and token.
     *
     * @param string $email Invite email.
     * @param string $token Invite token.
     *
     * @return Invite Invite object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByEmailToken(string $email, string $token): Invite
    {
        $sql = 'SELECT * FROM invite WHERE email = ? AND token = ?';
        $bindParams = [$email, $token];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Map a DB row into an Invite object.
     *
     * @param array $row DB row object.
     *
     * @return Invite Invite object.
     */
    protected function mapArray(array $row): Invite
    {
        $invite = new Invite();

        $invite->setIid($row['iid'] ?? 0);
        $invite->setCreated($row['created'] ?? '');
        $invite->setEmail($row['email'] ?? '');
        $invite->setToken($row['token'] ?? '');

        return $invite;
    }
}
