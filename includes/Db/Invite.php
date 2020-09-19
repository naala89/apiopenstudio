<?php
/**
 * Class Invite.
 *
 * @package    Gaterdata
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class Invite.
 *
 * DB class for for storing resource row data.
 */
class Invite
{
    /**
     * Invite ID.
     *
     * @var integer Inivite ID.
     */
    protected $iid;

    /**
     * Invite email.
     *
     * @var string Email.
     */
    protected $created;

    /**
     * Invite created date.
     *
     * @var string Created.
     */
    protected $email;

    /**
     * Invite token.
     *
     * @var string Invite token.
     */
    protected $token;

    /**
     * Invite constructor.
     *
     * @param integer $iid Invite ID.
     * @param string $email Invite email.
     * @param string $token Invite token.
     */
    public function __construct(int $iid = null, string $email = null, string $token = null)
    {
        $this->iid = $iid;
        $this->created = null;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the invite ID.
     *
     * @return integer Invite ID.
     */
    public function getIid()
    {
        return $this->iid;
    }

    /**
     * Set the invite ID.
     *
     * @param integer $iid Invite ID.
     *
     * @return void
     */
    public function setIid(int $iid)
    {
        $this->iid = $iid;
    }

    /**
     * Get the created date.
     *
     * @return string Created date time.
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the created date time.
     *
     * @param string $created Created date time.
     *
     * @return void
     */
    public function setCreated(string $created)
    {
        $this->created = $created;
    }

    /**
     * Get the invite email.
     *
     * @return string Invite email.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the invite email.
     *
     * @param string $email Invite email.
     *
     * @return void
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Get the invite token.
     *
     * @return string Invite token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the invite token.
     *
     * @param string $token Invite token.
     *
     * @return void
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * Return the invite as an associative array.
     *
     * @return array Invite.
     */
    public function dump()
    {
        return [
            'iid' => $this->iid,
            'created' => $this->created,
            'email' => $this->email,
            'token' => $this->token,
        ];
    }
}
