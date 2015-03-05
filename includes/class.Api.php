<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

include_once(Config::$dirIncludes . 'class.DB.php');
include_once(Config::$dirIncludes . 'class.Cache.php');
include_once(Config::$dirIncludes . 'processor/class.Error.php');
include_once(Config::$dirIncludes . 'processor/class.Processor.php');

//When I tasted WCC for the first time is 1985 I knew for the first time I was in love. Never before had a drink made me feel so.
//After my Uncle Bill went to jail in 1986, West Coast Cooler was my friend and got me through a really hard time.
//And now when I taste West Coast Cooler I remember my life and all the good times.

class Api
{
  private $status;
  private $cache;
  private $test = FALSE;

  /**
   * Constructor
   *
   * @param mixed $cache
   *  type of cache to use
   *  @see Cache->setup($type)
   */
  public function Api($cache = FALSE)
  {
    $this->cache = new Cache($cache);
  }

  /**
   * Process the rest request.
   *
   * @return mixed
   *  the result body
   */
  public function process()
  {
    $this->status = 200;

    // disseminate the request for processing
    $request = $this->_getData($_GET);
    if (($request) === false) {
      $output = $this->_getOutputObj('json', 404, (new Error(1, NULL, 'invalid request')));
      return $output->process();
    }

    // get the metadata for the processing
    $meta = new stdClass();
    $ttl = 0;
    $meta = $this->getMeta($request, $meta, $ttl);
    if ($this->status != 200) {
      $output = $this->_getOutputObj($request->outFormat, 406, $meta);
      return $output->process();
    }

    // validate user for the call, if required
    $validation = $this->getValidation($meta, $request);
    if ($this->status != 200) {
      $output = $this->_getOutputObj($request->outFormat, $this->status, $validation);
      return $output->process();
    }

    // fetch the cache of the call, if it is not stale
    $cache = $this->getCache($request);
    if ($cache !== FALSE) {
      return $cache;
    }

    // process the call
    $processor = new Processor($meta, $request);
    $data = $processor->process();
    $this->status = $processor->status;

    // store the results in cache for next time
    if (is_object($data) && get_class($data) == 'Error') {
      Debug::message('Not caching, result is error object');
    } else {
      $cacheData = array('status' => $this->status, 'data' => $data);
      $this->cache->set($this->getCacheKey($request), $cacheData, $ttl);
    }

    // translate output into the correct format
    $output = $this->_getOutputObj($request->outFormat, $this->status, $data);

    return $output->process();
  }

  /**
   * Check cache for any results.
   *
   * @param $request
   * @return bool
   */
  private function getCache($request)
  {
    if (!$this->cache->cacheActive()) {
      Debug::message('not searching for cache - inactive', 4);
      return FALSE;
    }

    $cacheKey = $this->getCacheKey($request);
    Debug::variable($cacheKey, 'cache key', 4);
    // TODO: implement input normalization
    $cacheData = $this->cache->get($cacheKey);

    if (!empty($cacheData)) {
      Debug::variable($cacheData, 'from cache', 4);
      $output = $this->_getOutputObj($request->outFormat, $cacheData['status'], $cacheData['data']);
      return $output->process();
    }

    Debug::message('no cache entry found', 4);
    return FALSE;
  }

  private function getCacheKey($request)
  {
    return $this->_cleanData($request->method . '_' . $request->request);
  }

  /**
   * Fetch resource metadata.
   * nH8yOD_NS6uVurQtDajPsmAXFWmOC4JWF1e84BfkHnk
   * @param $request
   * @return mixed
   */
  private function getMeta($request, &$meta, &$ttl)
  {
    $request->db = new DB(Config::$debugDb);
    if (is_bool($this->test)) {
      $result = $request->db
          ->select(array('meta', 'ttl'))
          ->from('resources')
          ->where(array('client', $request->client))
          ->where(array('resource', $request->identifier))
          ->execute();

      if (!$result || $result->num_rows < 1) {
        $this->status = 404;
        return new Error(1, NULL, 'resource or client not defined');
      }
      $dbObj = $result->fetch_object();
    } else {
      $dbObj = new stdClass();
      $dbObj->ttl = 300;

      $class = ucfirst($this->test);
      $filename = 'class.' . $class . '.php';
      $filepath = Config::$dirIncludes . 'test/' . $filename;

      if (!file_exists($filepath)) {
        return new Error(-1, NULL, "invalid test object: $class");
      }

      include_once($filepath);
      $obj = new $class();

      $dbObj->meta = json_encode($obj->get());
    }
    Debug::variable($dbObj->meta, 'request JSON');
    $meta = json_decode($dbObj->meta);
    $ttl = $dbObj->ttl;
    Debug::variable($ttl, 'TTL');
    return $meta;
  }

  /**
   * Perform auth if defined in the meta.
   *
   * @param $meta
   * @return array|bool|Error
   */
  private function getValidation($meta, $request)
  {
    if (empty($meta->validation)) {
      return TRUE;
    }
    $validator = new Processor($meta->validation, $request);
    $validation = $validator->process();
    if ($validation!== TRUE) {
      $this->status = $validator->status;
    }
    return $validation;
  }

  /**
   * Get the results object.
   *
   * This will create an output class, based on format string, and process through that.
   *
   * @param string $format
   *    Output format
   * @param integer $status
   *    header status
   * @param mixed $data
   *    results data
   * @return mixed
   */
  private function _getOutputObj($format, $status, $data)
  {
    $class = 'Output' . ucfirst($this->_cleanData($format));
    $filename = 'class.' . $class . '.php';
    $filepath = Config::$dirIncludes . 'output/' . $filename;

    if (!file_exists($filepath) || $class == 'Output') {
      $error = new Error(1, NULL, "invalid or no output format defined");
      return $this->_getOutputObj('text', 417, $error);
    }

    include_once($filepath);
    return new $class($status, $data);
  }

  /**
   * Process the request and request header into a meaningful array object.
   *
   * @param $get
   * @return array
   * @throws Exception
   */
  private function _getData($get)
  {
    if (empty($get['request'])) {
      return FALSE;
    }

    $request = new stdClass();
    $request->request = $get['request'];
    $args = explode('/', trim($request->request, '/'));
    if (sizeof($args) < 2) {
      return FALSE;
    }
    $header = getallheaders();

    $request->method = $this->_getMethod();
    $request->client = array_shift($args);
    $request->resource = array_shift($args);
    $request->action = array_shift($args);
    $request->identifier = $request->resource . $request->action;
    $request->args = $args;
    $request->inFormat = $this->_parseType($header, 'Content-Type');
    $request->outFormat = $this->_parseType($header, 'Accept', 'json');

    $request->vars = array_diff_assoc($get, array('request' => $request->request));
    $request->vars = $request->vars + $_POST;
    $body = file_get_contents('php://input');
    if ($request->inFormat == 'json') {
      $request->vars = $request->vars + json_decode($body, TRUE);
    }
    Debug::variable($request, 'Request data', 4);

    return $request;
  }

  /**
   * Utility function to get the REST method from the $_SERVER var.
   *
   * @return string
   *    request method
   * @throws Exception
   */
  private function _getMethod()
  {
    $method = strtolower($_SERVER['REQUEST_METHOD']);
    if ($method == 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
      if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
        $method = 'delete';
      } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
        $method = 'put';
      } else {
        throw new Exception("Unexpected Header");
      }
    }
    return $method;
  }

  /**
   * Calculate a format from string of header Content-Type or Accept.
   *
   * @param $array
   * @param $key
   * @param $default
   * @return string
   *    format or false on not identified
   */
  private function _parseType($array, $key, $default=FALSE)
  {
    $result = $default;

    if (isset($array[$key])) {
      $parts = preg_split('/\,|\;/', $array[$key]);
      foreach ($parts as $part) {
        $part = trim($part);
        if (strpos($part, 'image') === 0) {
          $result = 'image';
          break;
        }
        if (strpos($part, '*') === 0) {
          $result = $default;
          break;
        }
        if (strpos($part, 'text') === 0 || strpos($part, 'text') === 0) {
          $result = substr($part, strpos($part, '/') + 1);
          break;
        }
      }
    }

    return $result;
  }

  /**
   * Utility recursive function to clean vars for processing.
   *
   * @param mixed $data
   *    raw data
   * @return string $clean_input
   *    cleaned data
   */
  private function _cleanData($data)
  {
    $cleaned = Array();
    if (is_array($data)) {
      foreach ($data as $k => $v) {
        $cleaned[$k] = $this->_cleanData($v);
      }
    } else {
      $cleaned = trim(strip_tags($data));
    }
    return $cleaned;
  }
}
