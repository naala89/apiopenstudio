<?php

namespace Gaterdata\Db;

/**
 * Class Resource.
 *
 * @package Gaterdata\Db
 */
class Resource
{
    /**
     * @var int Resource ID.
     */
    protected $resid;
    /**
     * @var int Application ID.
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
     * @param int $resid
     *   The resource ID.
     * @param int $appid
     *   The application ID.
     * @param string $name
     *   The resource name.
     * @param string $description
     *   The resource description.
     * @param string $method
     *   The resource method.
     * @param string $uri
     *   The resource URI.
     * @param string $meta
     *   The resource metadata.
     * @param string $ttl
     *   The resource TTL.
     */
    public function __construct(
        $resid = null,
        $appid = null,
        $name = null,
        $description = null,
        $method = null,
        $uri = null,
        $meta = null,
        $ttl = null
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
     * @return int
     *   The resource ID
     */
    public function getResid()
    {
        return $this->resid;
    }

    /**
     * Set the resource ID.
     *
     * @param int $resid
     *   The resource ID.
     */
    public function setResid($resid)
    {
        $this->resid = $resid;
    }

    /**
     * Get the application ID.
     *
     * @return int
     *   The application ID.
     */
    public function getAppId()
    {
        return $this->appid;
    }

    /**
     * Set the resource application ID.
     *
     * @param int $appid
     *   The application ID.
     */
    public function setAppId($appid)
    {
        $this->appid = $appid;
    }

    /**
     * Get the resource nanme.
     *
     * @return string
     *   The resource name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the resource name.
     *
     * @param string $name
     *   The resource name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the resource description.
     *
     * @return string
     *   The resource description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the resource description.
     *
     * @param string $description
     *   The resource description.
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the resource method.
     *
     * @return string
     *   Resource method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the resource method.
     *
     * @param string $method
     *   The resource method.
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Get the resource URI.
     *
     * @return string
     *   The  resource URI.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the resource URI.
     *
     * @param string $uri
     *   Resource URI.
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Get the json encoded resource metadata.
     *
     * @return string
     *   Json encoded resource metadata.
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set the json encoded resource metadata.
     *
     * @param string $meta
     *   The json encoded resource metadata.
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * Get the resource TTL.
     *
     * @return int
     *   Time to live.
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set the TTL.
     *
     * @param int $ttl
     *   Time to live.
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array
     *   Api resource.
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
