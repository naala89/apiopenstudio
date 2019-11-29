<?php

/**
 * Perform input from external source
 */

namespace Gaterdata\Endpoint;
use Gaterdata\Core;

class Url extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = array(
    'name' => 'Url',
    'machineName' => 'url',
    'description' => 'Fetch the result form an external URL.',
    'menu' => 'Endpoint',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post'),
        'default' => ''
      ),
      'url' => array(
        'description' => 'The source URL.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'sourceType' => array(
        'description' => 'Manually declare the source type (the fastest), or allow Datagator to detect the type ("auto"). \
         If auto is selected, CSV and invalid JSON/XML will be treated as text.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('xml', 'json', 'text', 'html', 'auto'),
        'default' => 'auto'
      ),
      'body' => array(
        'description' => 'The body of the request.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'auth' => array(
        'description' => 'The remote authentication process.',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array('AuthCookie', 'AuthOauthHeader', 'AuthBasic', 'AuthDigest'),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'reportError' => array(
        'description' => 'Stop processing if the remote source responds with an error.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
      'connectTimeout' => array(
        'description' => 'The number of seconds to wait while trying to connect. Indefinite wait time of 0 is disallowed (optional).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => ''
      ),
      'timeout' => array(
        'description' => 'The maximum number of seconds to allow the remote call to execute (optional). This time will include connectTimeout value.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => ''
      )
    ),
  );

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Endpoint Url', 4);

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
      throw new Core\ApiException('could not get response from remote server: ' . $curl->errorMsg, 5, $this->id, $curl->httpStatus);
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
   * @param $str
   * @return mixed|string
   */
  protected function _get_curlopt_from_string($str)
  {
    $str = strtoupper($str);
    if (preg_match('/^CURLOPT_/', $str) && defined($str)) {
      return eval("return $str;");
    }
    return $str;
  }
}
