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
 *    "curlOpts": <obj>
 *  },
 * }
 */

namespace Datagator\Processor;
use Datagator\Core;

class InputUrl extends ProcessorBase
{
  protected $required = array('method', 'source');
  public $details = array(
    'name' => 'External',
    'description' => 'Fetch the result form an external URL.',
    'menu' => 'internet',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"get"', '"post"'),
      ),
      'source' => array(
        'description' => 'Th source URL.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
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
    Core\Debug::variable($this->meta, 'processor InputUrl', 4);
    $this->validateRequired();

    $method = $this->getVar($this->meta->method);
    $method = strtolower($method);
    if (!in_array($method, array('get', 'post'))) {
      throw new Core\ApiException('empty or invalid HTTP method', 1, $this->id, 417);
    }

    $url = $this->getVar($this->meta->source);

    //get static curl options for this call
    $curlOpts = array();
    if (isset($this->meta->curlOpts)) {
      foreach ($this->meta->curlOpts as $k => $v) {
        $curlOpts += array($this->_get_curlopt_from_string($k) => $v);
      }
    }

    //get auth
    if (!empty($this->meta->auth)) {
      $class = 'Datagator\\Processor\\Auth' . ucfirst(trim($this->meta->auth));
      if (!class_exists($class)) {
        throw new Core\ApiException('invalid Auth: ' . $this->meta->auth);
      }
      $authenticator = new $class($this->meta->auth, $this->request);
      $authentication = $authenticator->process();
      $curlOpts += $authentication;
    }

    //add any params to post or get call
    if (!empty($this->meta->vars)) {
      $vars = array();
      foreach ($this->meta->vars as $key => $val) {
        $vars[$key] = $this->getVar($val);
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
      throw new Core\ApiException('could not get response from remote server: ' . $curl->errorMsg, $curl->curlStatus, $this->id, $curl->httpStatus);
    }
    //TODO: use $curl->type to convert all inputUrl results into a standard format
    if ($curl->httpStatus != 200) {
      throw new Core\ApiException($result, 3, $this->id, $curl->httpStatus);
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
