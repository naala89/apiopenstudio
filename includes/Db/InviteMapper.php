<?php
/**
 * Class InviteMapper.
 *
 * @package Gaterdata\Db
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @author john89
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;

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
     * @param \Gaterdata\Db\Invite $invite Invite object.
     *
     * @return boolean Success.
     *
     * @throws \Gaterdata\Core\ApiException Return an ApiException on DB error.
     */
    public function save(Invite $invite)
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
    public function delete(Invite $invite)
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
    public function findAll(array $params)
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
    public function findByIid(int $iid)
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
     * @return array Array of Invite.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByEmail(string $email)
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
    public function findByToken(string $token)
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
    public function findByEmailToken(string $email, string $token)
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
