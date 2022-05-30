<?php

/**
 * Class VarStore.
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

/**
 * Class VarStore.
 *
 * DB class for storing var row data.
 */
class VarStore
{
    /**
     * @var integer|null Var store ID.
     */
    protected ?int $vid;

    /**
     * @var integer|null account ID.
     */
    protected ?int $accid;

    /**
     * @var integer|null Application ID.
     */
    protected ?int $appid;

    /**
     * @var string|null Var key.
     */
    protected ?string $key;

    /**\
     * @var mixed Var value.
     */
    protected $val;

    /**
     * VarStore constructor.
     *
     * @param int|null $vid The var ID.
     * @param int|null $accid The var account ID.
     * @param int|null $appid The var application ID.
     * @param string|null $key The var key.
     * @param mixed $val The var value.
     */
    public function __construct(int $vid = null, int $accid = null, int $appid = null, string $key = null, $val = null)
    {
        $this->vid = $vid;
        $this->accid = $accid;
        $this->appid = $appid;
        $this->key = $key;
        $this->val = $val;
    }

    /**
     * Get the var ID.
     *
     * @return integer|null The var ID.
     */
    public function getVid(): ?int
    {
        return $this->vid;
    }

    /**
     * Set the var ID.
     *
     * @param integer|null $vid The var ID.
     *
     * @return void
     */
    public function setVid(?int $vid)
    {
        $this->vid = $vid;
    }

    /**
     * Get the var account ID.
     *
     * @return integer|null The var account ID.
     */
    public function getAccid(): ?int
    {
        return $this->accid;
    }

    /**
     * Set the var account ID.
     *
     * @param integer|null $accid The var account ID.
     *
     * @return void
     */
    public function setAccid(?int $accid)
    {
        $this->accid = $accid;
    }

    /**
     * Get the var application ID.
     *
     * @return integer|null The var application ID.
     */
    public function getAppid(): ?int
    {
        return $this->appid;
    }

    /**
     * Set the var application ID.
     *
     * @param integer|null $appid The avr application ID.
     *
     * @return void
     */
    public function setAppid(?int $appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the var key.
     *
     * @return string|null
     *   The var key.
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Set the var key.
     *
     * @param string|null $key
     *   The var key.
     *
     * @return void
     */
    public function setKey(?string $key)
    {
        $this->key = $key;
    }

    /**
     * Get the var value.
     *
     * @return string|null
     *   The var value.
     */
    public function getVal(): ?string
    {
        return $this->val;
    }

    /**
     * Set the var value.
     *
     * @param mixed $val The var value.
     *
     * @return void
     */
    public function setVal($val)
    {
        $this->val = $val;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Associative array of var attributes.
     */
    public function dump(): array
    {
        return [
            'vid' => $this->vid,
            'accid' => $this->accid,
            'appid' => $this->appid,
            'key' => $this->key,
            'val' => $this->val,
        ];
    }
}
