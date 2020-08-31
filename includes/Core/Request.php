<?php
/**
 * Class Request.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

/**
 * Class Request
 *
 * Container class for all request details.
 */
class Request
{
    /**
     * @var integer Account ID.
     */
    private $accId;

    /**
     * @var string Account name.
     */
    private $accName;

    /**
     * @var integer Application ID.
     */
    private $appId;

    /**
     * @var string Application name.
     */
    private $appName;

    /**
     * @var array Request arguments.
     */
    private $args;

    /**
     * @var string Cache key.
     */
    private $cacheKey;

    /**
     * @var array Server $_FILES.
     */
    private $files;

    /**
     * @var array Fragments metadata and results.
     */
    private $fragments = [];

    /**
     * @var array GET variables.
     */
    private $getVars;

    /**
     * @var string Request IP.
     */
    private $ip;

    /**
     * @var \stdClass Request metadata.
     */
    private $meta;

    /**
     * @var string Request method.
     */
    private $method;

    /**
     * @var string Value of the accept header, translated into readable string.
     */
    private $outFormat;

    /**
     * @var array POST variables.
     */
    private $postVars;

    /**
     * @var string Resource JSON string.
     */
    private $resource;

    /**
     * @var integer Cache TTL.
     */
    private $ttl = 0;

    /**
     * @var string Requesting URI.
     */
    private $uri;

    /**
     * @param integer $var Account ID.
     *
     * @return void
     */
    public function setAccId(int $var)
    {
        $this->accId = $var;
    }

    /**
     * @return integer
    */
    public function getAccId()
    {
        return $this->accId;
    }

    /**
     * @param string $var Account name.
     *
     * @return void
     */
    public function setAccName(string $var)
    {
        $this->accName = $var;
    }

    /**
     * @return string
     */
    public function getAccName()
    {
        return $this->accName;
    }

    /**
     * @param integer $var Application ID.
     *
     * @return void
     */
    public function setAppId(int $var)
    {
        $this->appId = $var;
    }

    /**
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $var Application name.
     *
     * @return void
     */
    public function setAppName(string $var)
    {
        $this->appName = $var;
    }

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param string $var Request URI.
     *
     * @return void
     */
    public function setUri(string $var)
    {
        $this->uri = $var;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $var Request method.
     *
     * @return void
     */
    public function setMethod(string $var)
    {
        $this->method = $var;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param array $var Request URI args.
     *
     * @return void
     */
    public function setArgs(array $var)
    {
        $this->args = $var;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param array $var Copy of request $_GET.
     *
     * @return void
     */
    public function setGetVars(array $var)
    {
        $this->getVars = $var;
    }

    /**
     * @return array
     */
    public function getGetVars()
    {
        return $this->getVars;
    }

    /**
     * @param array $var Copy of request $_POST.
     *
     * @return void
     */
    public function setPostVars(array $var)
    {
        $this->postVars = $var;
    }

    /**
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

    /**
     * @param array $var Copy of request $_FILES.
     *
     * @return void
     */
    public function setFiles(array $var)
    {
        $this->files = $var;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param string $var Request IP address.
     *
     * @return void
     */
    public function setIp(string $var)
    {
        $this->ip = $var;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $var Output format.
     *
     * @return void
     */
    public function setOutFormat(string $var)
    {
        $this->outFormat = $var;
    }

    /**
     * @return string
     */
    public function getOutFormat()
    {
        return $this->outFormat;
    }

    /**
     * @param object $var Resource JSON.
     *
     * @return void
     */
    public function setResource(object $var)
    {
        $this->resource = $var;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param \stdClass $var Resource metadata.
     *
     * @return void
     */
    public function setMeta(\stdClass $var)
    {
        $this->meta = $var;
    }

    /**
     * @return \stdClass
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param integer $var Cache TTL.
     *
     * @return void
     */
    public function setTtl(int $var)
    {
        $this->ttl = $var;
    }

    /**
     * @return integer
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param array $var Fragments.
     *
     * @return void
     */
    public function setFragments(array $var)
    {
        $this->fragments = $var;
    }

    /**
     * @return array
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * @param string $var Cache key.
     *
     * @return void
     */
    public function setCacheKey(string $var)
    {
        $this->cacheKey = $var;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
