<?php
/**
 * Class Resource.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

/**
 * Class Resource.
 *
 * DB class for for storing resource row data.
 */
class Resource
{
    /**
     * @var integer Resource ID.
     */
    protected $resid;

    /**
     * @var integer Application ID.
     */
    protected $appid;

    /**
     * @var string Resource name.
     */
    protected $name;

    /**
     * @var string Resource description.
     */
    protected $description;

    /**
     * @var string Resource method.
     */
    protected $method;

    /**
     * @var string Resource URI.
     */
    protected $uri;

    /**
     * @var string Resource metadata.
     */
    protected $meta;

    /**
     * @var string Resource time to live.
     */
    protected $ttl;

    /**
     * Resource constructor.
     *
     * @param integer $resid The resource ID.
     * @param integer $appid The application ID.
     * @param string $name The resource name.
     * @param string $description The resource description.
     * @param string $method The resource method.
     * @param string $uri The resource URI.
     * @param string $meta The resource metadata.
     * @param string $ttl The resource TTL.
     */
    public function __construct(
        int $resid = null,
        int $appid = null,
        string $name = null,
        string $description = null,
        string $method = null,
        string $uri = null,
        string $meta = null,
        string $ttl = null
    ) {
        $this->resid = $resid;
        $this->appid = $appid;
        $this->name = $name;
        $this->description = $description;
        $this->method = $method;
        $this->uri = $uri;
        $this->meta = $meta;
        $this->ttl = $ttl;
    }

    /**
     * Get the resource ID.
     *
     * @return integer The resource ID
     */
    public function getResid()
    {
        return $this->resid;
    }

    /**
     * Set the resource ID.
     *
     * @param integer $resid The resource ID.
     *
     * @return void
     */
    public function setResid(int $resid)
    {
        $this->resid = $resid;
    }

    /**
     * Get the application ID.
     *
     * @return integer The application ID.
     */
    public function getAppId()
    {
        return $this->appid;
    }

    /**
     * Set the resource application ID.
     *
     * @param integer $appid The application ID.
     *
     * @return void
     */
    public function setAppId(int $appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the resource nanme.
     *
     * @return string The resource name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the resource name.
     *
     * @param string $name The resource name.
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the resource description.
     *
     * @return string The resource description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the resource description.
     *
     * @param string $description The resource description.
     *
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get the resource method.
     *
     * @return string Resource method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the resource method.
     *
     * @param string $method The resource method.
     *
     * @return void
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * Get the resource URI.
     *
     * @return string The  resource URI.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the resource URI.
     *
     * @param string $uri Resource URI.
     *
     * @return void
     */
    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * Get the json encoded resource metadata.
     *
     * @return string Json encoded resource metadata.
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set the json encoded resource metadata.
     *
     * @param string $meta The json encoded resource metadata.
     *
     * @return void
     */
    public function setMeta(string $meta)
    {
        $this->meta = $meta;
    }

    /**
     * Get the resource TTL.
     *
     * @return string Time to live.
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set the TTL.
     *
     * @param string $ttl Time to live.
     *
     * @return void
     */
    public function setTtl(string $ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Api resource.
     */
    public function dump()
    {
        return [
            'resid' => $this->resid,
            'name' => $this->name,
            'description' => $this->description,
            'appid' => $this->appid,
            'method' => $this->method,
            'uri' => $this->uri,
            'ttl' => $this->ttl,
            'meta' => $this->meta,
        ];
    }
}
