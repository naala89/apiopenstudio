<?php

/**
 * Class Account.
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
 * Class Account.
 *
 * DB class for for storing account row data.
 */
class Application
{
    /**
     * Application ID.
     *
     * @var integer|null Application ID.
     */
    protected ?int $appid;

    /**
     * Account ID the application belongs to.
     *
     * @var integer|null Account ID.
     */
    protected ?int $accid;

    /**
     * Application name.
     *
     * @var string|null Application name.
     */
    protected ?string $name;

    /**
     * OpenApi schema fragment.
     *
     * @var string|null OpenApi schema fragment.
     */
    protected ?string $openapi;

    /**
     * Application constructor.
     *
     * @param int|null $appid Application ID.
     * @param int|null $accid Account ID.
     * @param string|null $name Application name.
     * @param string|null $openapi OpenApi JSON fragment.
     */
    public function __construct(int $appid = null, int $accid = null, string $name = null, string $openapi = null)
    {
        $this->appid = $appid;
        $this->accid = $accid;
        $this->name = $name;
        $this->openapi = $openapi;
    }

    /**
     * Get application IOD.
     *
     * @return integer Application ID.
     */
    public function getAppid(): ?int
    {
        return $this->appid;
    }

    /**
     * Set the application ID.
     *
     * @param integer $appid Application ID.
     *
     * @return void
     */
    public function setAppid(int $appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the account ID.
     *
     * @return integer Account ID.
     */
    public function getAccid(): ?int
    {
        return $this->accid;
    }

    /**
     * Set the account ID.
     *
     * @param integer $accid Account ID.
     *
     * @return void
     */
    public function setAccid(int $accid)
    {
        $this->accid = $accid;
    }

    /**
     * Get the application name.
     *
     * @return string Application name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the application name.
     *
     * @param string $name Application name.
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the OpenApi schema fragment.
     *
     * @return string OpenApi schema fragment.
     */
    public function getOpenapi(): ?string
    {
        return $this->openapi;
    }

    /**
     * Set the OpenApi schema fragment.
     *
     * @param string $openapi OpenApi schema fragment.
     *
     * @return void
     */
    public function setOpenapi(string $openapi)
    {
        $this->openapi = $openapi;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Application.
     */
    public function dump(): array
    {
        return [
            'appid' => $this->appid,
            'accid' => $this->accid,
            'name' => $this->name,
            'openapi' => $this->openapi,
        ];
    }
}
