<?php
/**
 * Class Url.
 *
 * @package Gaterdata
 * @subpackage Endpoint
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

/**
 * Class Url
 *
 * Provide the results from a remote endpoint.
 */
class Url extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Url',
        'machineName' => 'url',
        'description' => 'Fetch the result form an external URL.',
        'menu' => 'Endpoint',
        'input' => [
            'method' => [
                'description' => 'The HTTP method.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post'],
                'default' => '',
            ],
            'url' => [
                'description' => 'The source URL.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'sourceType' => [
                // phpcs:ignore
                'description' => 'Manually declare the source type (the fastest), or allow Datagator to detect the type ("auto"). If auto is selected, CSV and invalid JSON/XML will be treated as text.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['xml', 'json', 'text', 'html', 'auto'],
                'default' => 'auto',
            ],
            'body' => [
                'description' => 'The body of the request.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'auth' => [
                'description' => 'The remote authentication process.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => ['AuthCookie', 'AuthOauthHeader', 'AuthBasic', 'AuthDigest'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'reportError' => [
                'description' => 'Stop processing if the remote source responds with an error.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'connectTimeout' => [
                // phpcs:ignore
                'description' => 'The number of seconds to wait while trying to connect. Indefinite wait time of 0 is disallowed (optional).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'timeout' => [
                // phpcs:ignore
                'description' => 'The maximum number of seconds to allow the remote call to execute (optional). This time will include connectTimeout value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $method = $this->val('method', true);
        $connectTimeout = $this->val('connectTimeout', true);
        $timeout = $this->val('timeout', true);
        $url = $this->val('url', true);
        $body = $this->val('body', true);
        $reportError = $this->val('reportError', true);
        $sourceType = $this->val('sourceType', true);
        $auth = $this->val('auth', true);

        //get static curl options for this call
        $curlOpts = array();
        if ($connectTimeout > 0) {
            $curlOpts[] = [CURLOPT_CONNECTTIMEOUT => $connectTimeout];
        }
        if ($timeout > 0) {
            $curlOpts[] = [CURLOPT_TIMEOUT => $timeout];
        }
        if (!empty($body)) {
            $curlOpts[] = [CURLOPT_POSTFIELDS => $body];
        }

        //get auth
        if (!empty($auth)) {
            $curlOpts += $auth;
        }

        //send request
        $curl = new Core\Curl();
        $this->data = $curl->$method($url, $curlOpts);
        if ($this->data === false) {
            throw new Core\ApiException('could not get response from remote server: '
            . $curl->errorMsg, 5, $this->id, $curl->httpStatus);
        }
        if ($reportError && $curl->httpStatus != 200) {
            throw new Core\ApiException(json_encode($this->data), 5, $this->id, $curl->httpStatus);
        }

        if ($sourceType == 'auto') {
            $sourceType = $this->_calcFormat();
        }

        return new Core\DataContainer($this->data, $sourceType);
    }

    /**
     * @return string
     */
    private function _calcFormat()
    {
        $data = $this->data;
        // test for array
        if (is_array($data)) {
            return 'array';
        }
        // test for JSON
        json_decode($data);
        if (json_last_error() == JSON_ERROR_NONE) {
            return 'json';
        }
        // test for XML
        if (simplexml_load_string($data) !== false) {
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
