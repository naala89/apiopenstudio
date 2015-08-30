<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

namespace Datagator\Core;
use Datagator\Config;
use Datagator\Processors;
use Datagator\Db;
use Datagator\Validators;
use Datagator\Outputs;
use Spyc;

Debug::setup((Config::$debugInterface == 'HTML' ? Debug::HTML : Debug::LOG), Config::$debug, Config::$errorLog);

//When I tasted WCC for the first time is 1985 I knew for the first time I was in love. Never before had a drink made me feel so.
//After my Uncle Bill went to jail in 1986, West Coast Cooler was my friend and got me through a really hard time.
//And now when I taste West Coast Cooler I remember my life and all the good times.

class Api
{
  private $cache;
  private $test = false; // FALSE or name of yaml file in /yaml

  /**
   * Constructor
   *
   * @param mixed $cache
   *  type of cache to use
   *  @see Cache->setup($type)
   */
  public function __construct($cache=FALSE)
  {
    $this->cache = new Cache($cache);
  }

  /**
   * Process the rest request.
   *
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    // disseminate the request for processing
    $request = $this->_getData($_GET);

    // get the resource for the processing
    $result = $this->_getResource($request);
    $resource = $result->r;
    $ttl = $result->ttl;

    // validate user for the call, if required
    $this->_getValidation($resource, $request);

    // fetch the cache of the call, if it is not stale
    $cache = $this->_getCache($resource, $request);
    if ($cache !== FALSE) {
      return $cache;
    }

    // process the call
    if (empty($resource->process)) {
      throw new ApiException('invalid resource - process missing');
    }
    $processor = new Processors\ProcessorBase($resource->process, $request);
    $data = $processor->process();

    // store the results in cache for next time
    if (is_object($data) && get_class($data) == 'Error') {
      Debug::message('Not caching, result is error object');
    } else {
      $cacheData = array('data' => $data);
      $this->cache->set($this->_getCacheKey($request), $cacheData, $ttl);
    }

    return $this->_getOutput($resource, $request, $data);
  }

  private function _getOutput($resource, $request, $data)
  {
    // default to response output if no output defined
    $outputs = empty($resource->output) ? array('response') : $resource->output;
    $result = '';
    foreach ($outputs as $type => $meta) {
      if ($type == 'response') {
        //translate the output to the correct format as requested in header and return in the response
        $class = 'Datagator\\Outputs\\' . ucfirst($this->_cleanData($request->outFormat));
        $obj = new $class($data, 200);
        $result = $obj->process();
      } else {
        $class = 'Datagator\\Outputs\\' . ucfirst($this->_cleanData($type));
        $obj = new $class($data, 200, $meta);
        $obj->process();
      }
    }
    return $result;
  }

  /**
   * Check cache for any results.
   *
   * @param $resource
   * @param $request
   * @return bool
   */
  private function _getCache($resource, $request)
  {
    if (!$this->cache->cacheActive()) {
      Debug::message('not searching for cache - inactive', 4);
      return FALSE;
    }

    $cacheKey = $this->_getCacheKey($request);
    Debug::variable($cacheKey, 'cache key', 4);
    $data = $this->cache->get($cacheKey);

    if (!empty($data)) {
      Debug::variable($data, 'from cache', 4);
      return $this->_getOutput($resource, $request, $data);
    }

    Debug::message('no cache entry found', 4);
    return FALSE;
  }

  private function _getCacheKey($request)
  {
    return $this->_cleanData($request->method . '_' . $request->request);
  }

  /**
   * Get the requested resource and TTL from the DB.
   *
   * @param $request
   * @return \stdClass
   * @throws \Datagator\Core\ApiException
   */
  private function _getResource(&$request)
  {
    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $request->db = \ADONewConnection($dsn);
    if (!$request->db) {
      throw new ApiException('DB connection failed',1 , -1, 404);
    }
    $request->db->debug = Config::$debugDb;
    $mapper = new Db\ResourceMapper($request->db);

    $result = new \stdClass();
    if (!$this->test) {
      $resource = $mapper->findByAppIdMethodIdentifier($request->appId, $request->method, $request->identifier);

      if ($resource->getId() === NULL) {
        throw new ApiException('resource or client not defined', 1, -1, 404);
      }

      $result->r = json_decode($resource->getMeta());
      $result->ttl = $resource->getTtl();
    } else {
      $filepath = Config::$dirYaml . $this->test . '.yaml';
      if (!file_exists($filepath)) {
        throw new ApiException("invalid test yaml: $filepath", -1 , -1, 400);
      }
      $array = Spyc::YAMLLoad($filepath);
      $result = new \stdClass();
      $result->r = new \stdClass();
      $result->r->process = $this->_arrayToObject($array['process']);
      if (!empty($array['validation'])) {
        $result->r->validation = $this->_arrayToObject($array['validation']);
      }
      if (!empty($array['output'])) {
        $result->r->output = $this->_arrayToObject($array['output']);
      }
      $result->ttl = $array['ttl'];
      $request->method = $array['method'];
      $request->identifier = strtolower($array['uri']['noun']) . strtolower($array['uri']['verb']);
    }

    Debug::variable($result, 'resource');

    return $result;
  }

  /**
   * Perform api request auth if defined in the meta.
   *
   * @param $resource
   * @param $request
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _getValidation($resource, $request)
  {
    if (empty($resource->validation)) {
      return;
    }
    $class = 'Datagator\\Validators\\' . ucfirst($this->_cleanData($resource->validation->processor));
    $validator = new $class($resource->validation->meta, $request);
    if (!$validator->process()) {
      throw new ApiException('unauthorized', $resource->validation->meta->id, -1, 401);
    }
    return;
  }

  /**
   * Process the request and request header into a meaningful array object.
   *
   * @param $get
   * @return bool|\stdClass
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Core\Exception
   */
  private function _getData($get)
  {
    if (empty($get['request'])) {
      return FALSE;
    }

    $request = new \stdClass();
    $request->request = $get['request'];
    $args = explode('/', trim($request->request, '/'));
    if (sizeof($args) < 2) {
      throw new ApiException('invalid request');
    }
    $header = getallheaders();

    $request->ip = $_SERVER['REMOTE_ADDR'];
    $request->method = $this->_getMethod();
    if($request->method == 'options') {
      // this is a preflight request - respond with 200 and empty payload
      die();
    }
    $request->appId = array_shift($args);
    $request->resource = array_shift($args);
    $request->action = array_shift($args);
    $request->identifier = $request->resource . $request->action;
    $request->args = $args;
    $request->inFormat = $this->parseType($header, 'Content-Type');
    $request->outFormat = $this->parseType($header, 'Accept', 'json');
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
   * @throws \Datagator\Core\Exception
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
   * @param bool|FALSE $default
   * @return bool|string
   */
  public function parseType($array, $key, $default=FALSE)
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

    Debug::variable($result, $key, 4);

    return $result;
  }

  /**
   * Utility recursive function to clean vars for processing.
   *
   * @param $data
   * @return array|string
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

  private function _arrayToObject($array)
  {
    $json = json_encode($array);
    $object = json_decode($json);
    return $object;
  }
}
