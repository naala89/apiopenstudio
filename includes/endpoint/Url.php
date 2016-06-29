<?php

/**
 * Perform input from external source
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;

class Url extends Processor\ProcessorEntity
{
  protected $details = array(
    'name' => 'Url',
    'description' => 'Fetch the result form an external URL.',
    'menu' => 'Endpoint',
    'application' => 'Common',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', '"get"', '"post"'),
      ),
      'source' => array(
        'description' => 'The source URL.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'auth' => array(
        'description' => 'The remote authentication process.',
        'cardinality' => array(0, 1),
        'accepts' => array('function'),
      ),
      'reportError' => array(
        'description' => 'Stop processing if the remote source responds with an error.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', '"true"', '"false"'),
      ),
      'normalise' => array(
        'description' => 'If set to false, the results will pass though as a raw string. If set to 1\true, the results will be parsed into a format that can be processed further, i.e. merge, filter, etc.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', '"true"', '"false"'),
      ),
      'connectTimeout' => array(
        'description' => 'The number of seconds to wait while trying to connect. Indefinite wait time if 0 is disallowed (optional).',
        'cardinality' => array(0, 1),
        'accepts' => array('function', 'integer'),
      ),
      'timeout' => array(
        'description' => 'The maximum number of seconds to allow the remote call to execute (optional). This time will include connectTimeout value.',
        'cardinality' => array(0, 1),
        'accepts' => array('function', 'integer'),
      ),
//      'retry' => array(
//        'description' => 'The number of times to attempt the call on failure (optional). The default is 1.',
//        'cardinality' => array(0, 1),
//        'accepts' => array('function', 'integer'),
//      ),
    ),
  );

  /**
   * Retrieve data from an endpoint URL.
   *
   * @return mixed
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'function Url', 4);

    $method = strtolower($this->val($this->meta->method));
    if (!in_array($method, array('get', 'post'))) {
      throw new Core\ApiException('invalid method', 6, $this->id, 417);
    }
    $connectTimeout = $this->val($this->meta->connectTimeout);
    $timeout = $this->val($this->meta->timeout);
//    $retry = $this->val($this->meta->retry);
    $url = $this->val($this->meta->source);
    $reportError = $this->val($this->meta->reportError);

    //get static curl options for this call
    $curlOpts = array();
    if ($connectTimeout > 0) {
      $curlOpts[] = [CURLOPT_CONNECTTIMEOUT => $connectTimeout];
    }
    if ($timeout > 0) {
      $curlOpts[] = [CURLOPT_TIMEOUT => $timeout];
    }
//    if ($retry > 1) {
//
//    }
    if (isset($this->meta->curlOpts)) {
      foreach ($this->meta->curlOpts as $k => $v) {
        $curlOpts += array($this->_get_curlopt_from_string($k) => $v);
      }
    }

    //get auth
    if (!empty($this->meta->auth)) {
      $class = 'Datagator\\Endpoint\\Auth' . ucfirst(trim($this->meta->auth));
      if (!class_exists($class)) {
        throw new Core\ApiException('invalid Auth: ' . $this->meta->auth, 6);
      }
      $authenticator = new $class($this->meta->auth, $this->request);
      $authentication = $authenticator->process();
      $curlOpts += $authentication;
    }

    //add any params to post or get call
    if (!empty($this->meta->vars)) {
      $vars = array();
      foreach ($this->meta->vars as $key => $val) {
        $vars[$key] = $this->val($val);
      }
      switch ($method) {
        case 'post':
          $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($vars);
          break;
        case 'get':
          $url .= http_build_query($vars, '?', '&');
          break;
      }
    }

    //send request
    $curl = new Core\Curl();
    $result = $curl->{strtolower($this->meta->method)}($url, $curlOpts);
    if ($result === false) {
      throw new Core\ApiException('could not get response from remote server: ' . $curl->errorMsg, 5, $this->id, $curl->httpStatus);
    }

    $doNormalise = $this->val($this->meta->normalise) == 'true';
    if ($doNormalise) {
      $normalise = new Core\Normalise();
      $normalise->set($result, $curl->type);
      $result = $normalise->normalise();
      if ($reportError && $curl->httpStatus != 200) {
        throw new Core\ApiException(json_encode($result), 5, $this->id, $curl->httpStatus);
      }
    }

    return $result;
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
