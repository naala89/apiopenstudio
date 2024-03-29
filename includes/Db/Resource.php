<?php

/**
 * Class Resource.
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
 * Class Resource.
 *
 * DB class for for storing resource row data.
 */
class Resource
{
    /**
     * Resource ID.
     *
     * @var integer|null Resource ID.
     */
    protected ?int $resid;

    /**
     * Resource application ID.
     *
     * @var integer|null Application ID.
     */
    protected ?int $appid;

    /**
     * Resource name.
     *
     * @var string|null Resource name.
     */
    protected ?string $name;

    /**
     * Resource description.
     *
     * @var string|null Resource description.
     */
    protected ?string $description;

    /**
     * Resource request method.
     *
     * @var string|null Resource method.
     */
    protected ?string $method;

    /**
     * Resource URI.
     *
     * @var string|null Resource URI.
     */
    protected ?string $uri;

    /**
     * Resource Metadata.
     *
     * @var string|null Resource metadata.
     */
    protected ?string $meta;

    /**
     * Resource TTL.
     *
     * @var int|null Resource time to live.
     */
    protected ?int $ttl;

    /**
     * OpenApi Path JSON.
     *
     * @var string|null OpenApi Path JSON.
     */
    protected ?string $openapi;

    /**
     * Resource constructor.
     *
     * @param int|null $resid The resource ID.
     * @param int|null $appid The application ID.
     * @param string|null $name The resource name.
     * @param string|null $description The resource description.
     * @param string|null $method The resource method.
     * @param string|null $uri The resource URI.
     * @param string|null $meta The resource metadata.
     * @param string|null $openapi OpenApi Path JSON.
     * @param int|null $ttl The resource TTL.
     */
    public function __construct(
        int $resid = null,
        int $appid = null,
        string $name = null,
        string $description = null,
        string $method = null,
        string $uri = null,
        string $meta = null,
        string $openapi = null,
        int $ttl = null
    ) {
        $this->resid = $resid;
        $this->appid = $appid;
        $this->name = $name;
        $this->description = $description;
        $this->method = $method;
        $this->uri = $uri;
        $this->meta = $meta;
        $this->openapi = $openapi;
        $this->ttl = $ttl;
    }

    /**
     * Get the resource ID.
     *
     * @return ?int The resource ID
     */
    public function getResid(): ?int
    {
        return $this->resid;
    }

    /**
     * Set the resource ID.
     *
     * @param int $resid The resource ID.
     *
     * @return void
     */
    public function setResid(int $resid): void
    {
        $this->resid = $resid;
    }

    /**
     * Get the application ID.
     *
     * @return ?int The application ID.
     */
    public function getAppId(): ?int
    {
        return $this->appid;
    }

    /**
     * Set the resource application ID.
     *
     * @param int $appid The application ID.
     *
     * @return void
     */
    public function setAppId(int $appid): void
    {
        $this->appid = $appid;
    }

    /**
     * Get the resource name.
     *
     * @return ?string The resource name.
     */
    public function getName(): ?string
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the resource description.
     *
     * @return ?string The resource description.
     */
    public function getDescription(): ?string
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
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the resource method.
     *
     * @return ?string Resource method.
     */
    public function getMethod(): ?string
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
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * Get the resource URI.
     *
     * @return ?string The  resource URI.
     */
    public function getUri(): ?string
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
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the json encoded resource metadata.
     *
     * @return ?string Json encoded resource metadata.
     */
    public function getMeta(): ?string
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
    public function setMeta(string $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * Get the json encoded OpenApi path fragment.
     *
     * @return ?string OpenApi path fragment.
     */
    public function getOpenapi(): ?string
    {
        return $this->openapi;
    }

    /**
     * Set the json encoded OpenApi path fragment.
     *
     * @param string $openapi The json encoded OpenApi path fragment.
     *
     * @return void
     */
    public function setOpenapi(string $openapi): void
    {
        $this->openapi = $openapi;
    }

    /**
     * Get the resource TTL.
     *
     * @return ?int Time to live.
     */
    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    /**
     * Set the TTL.
     *
     * @param int $ttl Time to live.
     *
     * @return void
     */
    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array Api resource.
     */
    public function dump(): array
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
            'openapi' => $this->openapi,
        ];
    }
}
