<?php

/**
 * Perform input from external source
 *
 * METADATA
 * {
 *  "type": "inputUrl",
 *  "meta": {
 *    "method": "get|post",
 *    "auth": {},
 *    "vars": {},
 *    "source": {},
 *    "curlOpt": {},
 *  },
 * }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class ProcessorInputUrl extends Processor
{
  public function ProcessorInputUrl($meta, $request)
  {
    parent::__construct($meta, $request);
  }

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorInputUrl', 4);
    $this->status = 200;

    //get method
    if (!isset($this->meta->method) || !in_array(strtolower($this->meta->method), array('get', 'post'))) {
      $this->status = 417;
      return new Error(-1, 'empty or invalid method');
    }
    $method = strtolower($this->meta->method);
    Debug::variable($method, 'method');

    //get URL
    $url = $this->_getURL();
    if ($this->status != 200) {
      return $url;
    }
    Debug::variable($url, 'url');

    $curlOpts = array();

    //get auth
    if (!empty($this->meta->auth)) {
      $auth = $this->_getAuth($this->meta->auth);
      $curlOpts += $auth;
    }

    //get curl options
    if (isset($this->meta->options)) {
      foreach ($this->meta->options as $k => $v) {
        $curlOpts += array($this->_get_curlopt_from_string($k) => $v);
      }
    }
    Debug::variable($curlOpts, 'custom CuRL options', 4);

    //add any params to post or get fields
    if (!empty($this->meta->vars)) {
      $params = $this->meta->vars;
      if (is_object($params)) {
        $params = array();
        foreach ($this->meta->vars as $k => $v) {
          $params[$k] = $v;
        }
      }
      switch ($method) {
        case 'post':
          $curlOpts[CURLOPT_POSTFIELDS] = $params;
          break;
        case 'get':
          $url .= '?' . is_array($params) ? http_build_query($params) : preg_replace('/\?/', '', $params);
          break;
      }
      Debug::variable($params, 'params', 4);
    }

    //process curl
    $curl = new Curl();
    $result = $curl->{strtolower($this->meta->method)}($url, $curlOpts);
    if($result === false) {
      $error = new Error($curl->curlStatus, 'Could not fetch from remote API: ' . $curl->errorMsg);
      $result = $error->process();
    }
    $this->status = $curl->httpStatus;

    return $result;
  }

  private function _getURL()
  {
    if (is_string($this->meta->source)) {
      return $this->meta->source;
    }
    $processor = $this->getProcessor($this->meta->source);
    if ($this->status != 200) {
      return $processor;
    }
    $url = $processor->process();
    $this->status = $processor->status;
    return $url;
  }

  private function _getAuth($auth)
  {
    $classname = 'Auth' . ucfirst(trim($auth->type));
    $filename = 'class.' . $classname . '.php';
    $filepath = Config::$dirIncludes . 'auth/' . $filename;
    if (!file_exists($filepath)) {
      $this->status = 417;
      return new Error(-1, 'Invalid auth (' . trim($auth->type) . ')');
    }

    include_once($filepath);
    $vars = !empty($this->request['post']) ? $this->request['post'] : !empty($this->request['get']) ? $this->request['get'] : NULL;
    $auth = new $classname(!empty($auth->map) ? $auth->map : NULL, $vars);
    return $auth->process($this->request);
  }

  protected function _get_curlopt_from_string($str) {
    $str = strtoupper($str);
    if (preg_match('/^CURLOPT_/', $str) && defined($str)) {
      return eval("return $str;");
    }
    return $str;
  }
}
