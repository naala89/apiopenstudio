<?php

/**
 * Class Invite.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

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
     * @var integer|null Inivite ID.
     */
    protected ?int $iid;

    /**
     * Invite email.
     *
     * @var string|null Email.
     */
    protected ?string $created;

    /**
     * Invite created date.
     *
     * @var string|null Created.
     */
    protected ?string $email;

    /**
     * Invite token.
     *
     * @var string|null Invite token.
     */
    protected ?string $token;

    /**
     * Invite constructor.
     *
     * @param int|null $iid Invite ID.
     * @param string|null $email Invite email.
     * @param string|null $token Invite token.
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
    public function getIid(): ?int
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
    public function getCreated(): ?string
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
    public function getEmail(): ?string
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
    public function getToken(): ?string
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
    public function dump(): array
    {
        return [
            'iid' => $this->iid,
            'created' => $this->created,
            'email' => $this->email,
            'token' => $this->token,
        ];
    }
}
