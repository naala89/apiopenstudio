<?php

/**
 * Class Request.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use ApiOpenStudio\Db\Resource;

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
    private int $accId;

    /**
     * Account name.
     *
     * @var string Account name.
     */
    private string $accName;

    /**
     * Application ID.
     *
     * @var integer Application ID.
     */
    private int $appId;

    /**
     * Application name.
     *
     * @var string Application name.
     */
    private string $appName;

    /**
     * Request arguments.
     *
     * @var array Request arguments.
     */
    private array $args;

    /**
     * Contents of $_FILES.
     *
     * @var array Server $_FILES.
     */
    private array $files;

    /**
     * Fragments array.
     *
     * @var array Fragments metadata and results.
     */
    private array $fragments = [];

    /**
     * Contents of $_GET.
     *
     * @var array GET variables.
     */
    private array $getVars;

    /**
     * The requesting IP.
     *
     * @var string Request IP.
     */
    private string $ip;

    /**
     * The request metadata.
     *
     * @var array Request metadata.
     */
    private array $meta;

    /**
     * The request method.
     *
     * @var string Request method.
     */
    private string $method;

    /**
     * Request output format.
     *
     * example:
     *   ['mimeType' => 'image', 'mimeSubType' => 'jpeg']
     *   ['mimeType' => 'image', 'mimeSubType' => 'png']
     *   ['mimeType' => 'json', 'mimeSubType' => '']
     *   ['mimeType' => 'xml', 'mimeSubType' => '']
     *   ['mimeType' => 'text', 'mimeSubType' => '']
     *   ['mimeType' => 'html', 'mimeSubType' => '']
     *
     * @var array Value of the `Accept` header, translated into readable string.
     */
    private array $outFormat;

    /**
     * Contents of $_POST.
     *
     * @var array POST variables.
     */
    private array $postVars;

    /**
     * The resource called.
     *
     * @var Resource Resource object.
     */
    private Resource $resource;

    /**
     * Resource cache TTL.
     *
     * @var integer Cache TTL.
     */
    private int $ttl = 0;

    /**
     * Request URI.
     *
     * @var string Requesting URI.
     */
    private string $uri;

    /**
     * Set the account ID.
     *
     * @param integer $var Account ID.
     *
     * @return void
     */
    public function setAccId(int $var): void
    {
        $this->accId = $var;
    }

    /**
     * Get the account ID.
     *
     * @return int
    */
    public function getAccId(): int
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
    public function setAccName(string $var): void
    {
        $this->accName = $var;
    }

    /**
     * Get the account name.
     *
     * @return string
     */
    public function getAccName(): string
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
    public function setAppId(int $var): void
    {
        $this->appId = $var;
    }

    /**
     * Get the application ID.
     *
     * @return int
     */
    public function getAppId(): int
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
    public function setAppName(string $var): void
    {
        $this->appName = $var;
    }

    /**
     * Get the application name.
     *
     * @return string
     */
    public function getAppName(): string
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
    public function setUri(string $var): void
    {
        $this->uri = $var;
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getUri(): string
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
    public function setMethod(string $var): void
    {
        $this->method = $var;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set the request args.
     *
     * @param array $args Request URI args.
     *
     * @return void
     */
    public function setArgs(array $args): void
    {
        foreach ($args as $index => $arg) {
            $args[$index] = $arg === 'null' ? null : urldecode($arg);
        }
        $this->args = $args;
    }

    /**
     * Get the request args.
     *
     * @return array
     */
    public function getArgs(): array
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
    public function setGetVars(array $var): void
    {
        $this->getVars = $var;
    }

    /**
     * Get the GET vars.
     *
     * @return array
     */
    public function getGetVars(): array
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
    public function setPostVars(array $var): void
    {
        $this->postVars = $var;
    }

    /**
     * Get the POST vars.
     *
     * @return array
     */
    public function getPostVars(): array
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
    public function setFiles(array $var): void
    {
        $this->files = $var;
    }

    /**
     * Get the FILES.
     *
     * @return array
     */
    public function getFiles(): array
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
    public function setIp(string $var): void
    {
        $this->ip = $var;
    }

    /**
     * Get the request IP.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Set the output format.
     *
     * @param array $var Output format.
     *
     * @return void
     */
    public function setOutFormat(array $var): void
    {
        $this->outFormat = $var;
    }

    /**
     * Get the output format.
     *
     * @return array
     */
    public function getOutFormat(): array
    {
        return $this->outFormat;
    }

    /**
     * Set the resource object.
     *
     * @param Resource $var Resource object.
     *
     * @return void
     */
    public function setResource(Resource $var): void
    {
        $this->resource = $var;
    }

    /**
     * Get the resource.
     *
     * @return Resource
     */
    public function getResource(): Resource
    {
        return $this->resource;
    }

    /**
     * Set the metadata.
     *
     * @param array $var Resource metadata.
     *
     * @return void
     */
    public function setMeta(array $var): void
    {
        $this->meta = $var;
    }

    /**
     * Get the metadata,
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Set the cache TTL.
     *
     * @param int $var Cache TTL.
     *
     * @return void
     */
    public function setTtl(int $var): void
    {
        $this->ttl = $var;
    }

    /**
     * Get the cache TTL.
     *
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * Set a fragment.
     *
     * @param string $key Fragment key.
     * @param mixed $val Fragment value.
     *
     * @return void
     */
    public function setFragment(string $key, $val): void
    {
        $this->fragments[$key] = $val;
    }

    /**
     * Get a fragment.
     *
     * @param string $key Fragment key.
     *
     * @return mixed
     *
     * @throws ApiException
     */
    public function getFragment(string $key)
    {
        if (!isset($this->fragments[$key])) {
            throw new ApiException("invalid fragment key: $key", 6, -1, 500);
        }
        return $this->fragments[$key];
    }

    /**
     * Set the fragments.
     *
     * @param array $fragments Fragments array.
     *
     * @return void
     */
    public function setFragments(array $fragments): void
    {
        $this->fragments = $fragments;
    }

    /**
     * Get the fragments.
     *
     * @return array
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }
}
