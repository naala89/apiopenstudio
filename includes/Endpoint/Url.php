<?php

/**
 * Class Url.
 *
 * @package    ApiOpenStudio\Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Endpoint;

use ApiOpenStudio\Core;

/**
 * Class Url
 *
 * Provide the results from a remote endpoint.
 */
class Url extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Url',
        'machineName' => 'url',
        'description' => 'Fetch the result form an external URL.',
        'menu' => 'Endpoint',
        'input' => [
            'method' => [
                'description' => 'The HTTP method.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post'],
                'default' => '',
            ],
            'url' => [
                'description' => 'The source URL.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'expected_type' => [
                // phpcs:ignore
                'description' => 'Manually declare the source type (the fastest), or allow ApiOpenStudio to detect the type ("auto"). If auto is selected, CSV and invalid JSON/XML will be treated as text.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['image', 'xml', 'json', 'text', 'html', 'auto'],
                'default' => 'auto',
            ],
            'body' => [
                'description' => 'The body of the request.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'auth' => [
                'description' => 'The remote authentication process.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['AuthCookie', 'AuthOauthHeader', 'AuthBasic', 'AuthDigest'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'report_error' => [
                'description' => 'Stop processing if the remote source responds with an error.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'connect_timeout' => [
                // phpcs:ignore
                'description' => 'The number of seconds to wait while trying to connect. Indefinite wait time of 0 is disallowed (optional).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'timeout' => [
                // phpcs:ignore
                'description' => 'The maximum number of seconds to allow the remote call to execute (optional). This time will include connectTimeout value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * @var string Curl response.
     */
    protected string $data;

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $method = $this->val('method', true);
        $connectTimeout = $this->val('connect_timeout', true);
        $timeout = $this->val('timeout', true);
        $url = $this->val('url', true);
        $body = $this->val('body', true);
        $reportError = $this->val('report_error', true);
        $expectedType = $this->val('expected_type', true);
        $auth = $this->val('auth', true);

        //get static curl options for this call
        $curlOpts = [];
        if ($connectTimeout > 0) {
            $curlOpts += [CURLOPT_CONNECTTIMEOUT => $connectTimeout];
        }
        if ($timeout > 0) {
            $curlOpts += [CURLOPT_TIMEOUT => $timeout];
        }
        if (!empty($body)) {
            $curlOpts += [CURLOPT_POSTFIELDS => $body];
        }

        //get auth
        if (!empty($auth)) {
            $curlOpts += $auth;
        }

        //send request
        $curl = new Core\Curl($this->logger);
        $this->data = $curl->$method($url, $curlOpts);
        if ($this->data === false) {
            throw new Core\ApiException('could not get response from remote server: '
            . $curl->errorMsg, 5, $this->id, $curl->httpStatus);
        }
        if ($reportError && $curl->httpStatus != 200) {
            throw new Core\ApiException(json_encode($this->data), 5, $this->id, $curl->httpStatus);
        }

        if ($expectedType == 'auto') {
            $expectedType = $this->calcFormat();
        }

        return new Core\DataContainer($this->data, $expectedType);
    }

    /**
     * Calculate the format of the data.
     *
     * @return string
     */
    private function calcFormat(): string
    {
        // test for array
        if (is_array($this->data)) {
            return 'array';
        }
        // test for JSON
        json_decode($this->data);
        if (json_last_error() == JSON_ERROR_NONE) {
            return 'json';
        }
        // test for XML
        if (simplexml_load_string($this->data) !== false) {
            return 'xml';
        }
        return 'text';
    }

    /**
     * Convert a CURL string constant to it's numerical equivalent.
     *
     * @param string $str Curl option constant.
     *
     * @return mixed Curl opt value.
     */
    protected function getCurloptFromString(string $str)
    {
        $str = strtoupper($str);
        if (preg_match('/^CURLOPT_/', $str) && defined($str)) {
            return eval("return $str;");
        }
        return $str;
    }
}
