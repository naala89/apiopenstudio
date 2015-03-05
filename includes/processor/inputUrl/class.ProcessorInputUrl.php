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

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . 'processor/class.Error.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class ProcessorInputUrl extends Processor
{
  protected $required = array('method', 'source');

  /**
   * retrieve data from an endpoint URL
   *
   * @return array|Error
   */
  public function process()
  {
    Debug::variable($this->meta, 'processorInputUrl', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    //get method
    $method = $this->getVar($this->meta->method);
    if ($this->status != 200) {
      return $method;
    }
    $method = strtolower($method);
    if (!in_array($method, array('get', 'post'))) {
      $this->status = 417;
      return new Error(1, $this->id, 'empty or invalid HTTP method');
    }

    //get URL
    $url = $this->getVar($this->meta->source);
    if ($this->status != 200) {
      return $url;
    }

    //get static curl options for this call
    $curlOpts = array();
    if (isset($this->meta->curlOpts)) {
      foreach ($this->meta->curlOpts as $k => $v) {
        $curlOpts += array($this->_get_curlopt_from_string($k) => $v);
      }
    }

    //get auth
    if (!empty($this->meta->auth)) {
      $authenticator = $this->getProcessor($this->meta->auth, Config::$dirIncludes . '/processor/input/auth', $prefix = 'Auth', $suffix = '.php');
      $authentication = $authenticator->process();
      if ($authenticator->status != 200) {
        $this->status = $authenticator->status;
        return $authentication;
      }
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
    $curl = new Curl();
    $result = $curl->{strtolower($this->meta->method)}($url, $curlOpts);
    if ($result === false) {
      $result = new Error($curl->curlStatus, $this->id, 'could not get response from remote server: ' . $curl->errorMsg);
    }
    //TODO: use $curl->type to convert all inputUrl results into a standard format
    $this->status = $curl->httpStatus;
    if ($this->status != 200) {
      $result = $this->wrapError(3, $result);
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
