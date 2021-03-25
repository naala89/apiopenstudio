<?php

/**
 * Class Request.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

/**
 * Class Request
 *
 * Container class for all request details.
 */
class Request
{
    /**
     * Account ID.
     *
     * @var integer Account ID.
     */
    private $accId;

    /**
     * Account name.
     *
     * @var string Account name.
     */
    private $accName;

    /**
     * Application ID.
     *
     * @var integer Application ID.
     */
    private $appId;

    /**
     * Application name.
     *
     * @var string Application name.
     */
    private $appName;

    /**
     * Request arguments.
     *
     * @var array Request arguments.
     */
    private $args;

    /**
     * Cache key.
     *
     * @var string Cache key.
     */
    private $cacheKey;

    /**
     * Contents of $_FILES.
     *
     * @var array Server $_FILES.
     */
    private $files;

    /**
     * Fragments array.
     *
     * @var array Fragments metadata and results.
     */
    private $fragments = [];

    /**
     * Contents of $_GET.
     *
     * @var array GET variables.
     */
    private $getVars;

    /**
     * The requesting IP.
     *
     * @var string Request IP.
     */
    private $ip;

    /**
     * The request metadata.
     *
     * @var \stdClass Request metadata.
     */
    private $meta;

    /**
     * The request method.
     *
     * @var string Request method.
     */
    private $method;

    /**
     * Request output format.
     *
     * @var string Value of the accept header, translated into readable string.
     */
    private $outFormat;

    /**
     * Contents of $_POST.
     *
     * @var array POST variables.
     */
    private $postVars;

    /**
     * Resource metadata string.
     *
     * @var string Resource JSON string.
     */
    private $resource;

    /**
     * Resource cache TTL.
     *
     * @var integer Cache TTL.
     */
    private $ttl = 0;

    /**
     * Request URI.
     *
     * @var string Requesting URI.
     */
    private $uri;

    /**
     * Set the account ID.
     *
     * @param integer $var Account ID.
     *
     * @return void
     */
    public function setAccId(int $var)
    {
        $this->accId = $var;
    }

    /**
     * Get the account ID.
     *
     * @return integer
    */
    public function getAccId()
    {
        return $this->accId;
    }

    /**
     * Set the account name.
     *
     * @param string $var Account name.
     *
     * @return void
     */
    public function setAccName(string $var)
    {
        $this->accName = $var;
    }

    /**
     * Get the account name.
     *
     * @return string
     */
    public function getAccName()
    {
        return $this->accName;
    }

    /**
     * Set the application ID.
     *
     * @param integer $var Application ID.
     *
     * @return void
     */
    public function setAppId(int $var)
    {
        $this->appId = $var;
    }

    /**
     * Get the application ID.
     *
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set the application name
     *
     * @param string $var Application name.
     *
     * @return void
     */
    public function setAppName(string $var)
    {
        $this->appName = $var;
    }

    /**
     * Get the application name.
     *
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Set the URI.
     *
     * @param string $var Request URI.
     *
     * @return void
     */
    public function setUri(string $var)
    {
        $this->uri = $var;
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the method.
     *
     * @param string $var Request method.
     *
     * @return void
     */
    public function setMethod(string $var)
    {
        $this->method = $var;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the request args.
     *
     * @param array $var Request URI args.
     *
     * @return void
     */
    public function setArgs(array $var)
    {
        $this->args = $var;
    }

    /**
     * Get the request args.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Set the GET vars.
     *
     * @param array $var Copy of request $_GET.
     *
     * @return void
     */
    public function setGetVars(array $var)
    {
        $this->getVars = $var;
    }

    /**
     * Get the GET vars.
     *
     * @return array
     */
    public function getGetVars()
    {
        return $this->getVars;
    }

    /**
     * Set the POST vars.
     *
     * @param array $var Copy of request $_POST.
     *
     * @return void
     */
    public function setPostVars(array $var)
    {
        $this->postVars = $var;
    }

    /**
     * Get the POST vars.
     *
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

    /**
     * Set the FILES.
     *
     * @param array $var Copy of request $_FILES.
     *
     * @return void
     */
    public function setFiles(array $var)
    {
        $this->files = $var;
    }

    /**
     * Get the FILES.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set the request IP.
     *
     * @param string $var Request IP address.
     *
     * @return void
     */
    public function setIp(string $var)
    {
        $this->ip = $var;
    }

    /**
     * Get the request IP.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the output format.
     *
     * @param string $var Output format.
     *
     * @return void
     */
    public function setOutFormat(string $var)
    {
        $this->outFormat = $var;
    }

    /**
     * Get the output format.
     *
     * @return string
     */
    public function getOutFormat()
    {
        return $this->outFormat;
    }

    /**
     * Set the resource JSON.
     *
     * @param object $var Resource JSON.
     *
     * @return void
     */
    public function setResource(object $var)
    {
        $this->resource = $var;
    }

    /**
     * Get the resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the metadata.
     *
     * @param \stdClass $var Resource metadata.
     *
     * @return void
     */
    public function setMeta(\stdClass $var)
    {
        $this->meta = $var;
    }

    /**
     * Get the metadata,
     *
     * @return \stdClass
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set the cache TTL.
     *
     * @param integer $var Cache TTL.
     *
     * @return void
     */
    public function setTtl(int $var)
    {
        $this->ttl = $var;
    }

    /**
     * Get the cache TTL.
     *
     * @return integer
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set the fragments.
     *
     * @param array $var Fragments.
     *
     * @return void
     */
    public function setFragments(array $var)
    {
        $this->fragments = $var;
    }

    /**
     * Get the fragments.
     *
     * @return array
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * Set the cache key.
     *
     * @param string $var Cache key.
     *
     * @return void
     */
    public function setCacheKey(string $var)
    {
        $this->cacheKey = $var;
    }

    /**
     * Get the cache key.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
