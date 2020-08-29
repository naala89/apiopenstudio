<?php
/**
 * Class Invite.
 *
 * @package Gaterdata\Db
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @author john89
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
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
     * @var integer Inivite ID.
     */
    protected $iid;

    /**
     * @var string Email.
     */
    protected $created;

    /**
     * @var string Created.
     */
    protected $email;

    /**
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
