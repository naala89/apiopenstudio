<?php

/**
 * Perform input from external source
 *
 * METADATA
 * {
 *  "type": "inputUrl",
 *  "meta": {
 *    "id: <integer>,
 *    "method": "get|post",
 *    "auth": <processor>,
 *    "vars": <processor|string|obj>,
 *    "source": <processor|string|obj>,
 *    "curlOpts": <obj>,
 *    "normalise": <0|1>
 *  },
 * }
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;

class Url extends Processor\ProcessorBase
{
  public $details = array(
    'name' => 'Url',
    'description' => 'Fetch the result form an external URL.',
    'menu' => 'Endpoint',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"get"', '"post"'),
      ),
      'source' => array(
        'description' => 'The source URL.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'auth' => array(
        'description' => 'The remote authentication process.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor'),
      ),
      'reportError' => array(
        'description' => 'Stop processing if the remote source responds with an error.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"0"', '"1"'),
      ),
      'normalise' => array(
        'description' => 'If set to 0, the results will pass though as a raw string. If set to 1, the results will be parsed into a format that can be processed further, i.e. merge, filter, etc.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"0"', '"1"'),
      ),
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
    Core\Debug::variable($this->meta, 'processor Url', 4);

    $method = $this->val($this->meta->method);
    $method = strtolower($method);
    if (!in_array($method, array('get', 'post'))) {
      throw new Core\ApiException('invalid method', 6, $this->id, 417);
    }

    $url = $this->val($this->meta->source);
    $reportError = $this->val($this->meta->reportError);

    //get static curl options for this call
    $curlOpts = array();
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

    if ($this->val($this->meta->normalise)) {
      $normalise = new Core\Normalise();
      $normalise->set($result);
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
