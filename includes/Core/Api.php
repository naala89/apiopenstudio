<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

namespace Datagator\Core;
use Datagator\Config;
use Datagator\Processors;
use Datagator\Validators;
use Datagator\Yaml;

Debug::setup((Config::$debugInterface == 'HTML' ? Debug::HTML : Debug::LOG), Config::$debug, Config::$errorLog);

//When I tasted WCC for the first time is 1985 I knew for the first time I was in love. Never before had a drink made me feel so.
//After my Uncle Bill went to jail in 1986, West Coast Cooler was my friend and got me through a really hard time.
//And now when I taste West Coast Cooler I remember my life and all the good times.

class Api
{
  private $cache;
  private $test = FALSE; // FALSE or name of test class

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
    $cache = $this->_getCache($request);
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

    // translate output into the correct format
    $output = $this->getOutputObj($request->outFormat, $data, 200);

    return $output->process();
  }

  /**
   * Check cache for any results.
   *
   * @param $request
   * @return bool
   */
  private function _getCache($request)
  {
    if (!$this->cache->cacheActive()) {
      Debug::message('not searching for cache - inactive', 4);
      return FALSE;
    }

    $cacheKey = $this->_getCacheKey($request);
    Debug::variable($cacheKey, 'cache key', 4);
    // TODO: implement input normalization
    $cacheData = $this->cache->get($cacheKey);

    if (!empty($cacheData)) {
      Debug::variable($cacheData, 'from cache', 4);
      $output = $this->getOutputObj($request->outFormat, $cacheData['status'], $cacheData['data']);
      return $output->process();
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
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _getResource($request)
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

    $resource = new \stdClass();
    if (!$this->test) {
      $sql = 'SELECT meta, ttl FROM resources WHERE client=? AND method=? AND resource=?';
      $recordSet = $request->db->Execute($sql, array($request->client, $request->method, $request->identifier));

      if (!$recordSet || $recordSet->RecordCount() < 1) {
        throw new ApiException('resource or client not defined',1 , -1, 404);
      }
      $row = $recordSet->fields;
      $resource->r = json_decode($row['meta']);
      $resource->ttl = $row['ttl'];
    } else {
      // @TODO: update Test/*.php classes to be resource Yaml documents that are translated here.
      $class = 'Datagator\\Yaml\\' . ucfirst($this->test);
      $obj = new $class();
      $resource->r = json_decode(json_encode($obj->get()));
      $resource->ttl = 300;
    }

    Debug::variable($resource, 'resource');

    return $resource;
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
    $class = 'Datagator\\Validators\\' . ucfirst($this->_cleanData($resource->validation->type));
    $validator = new $class($resource->validation->meta, $request);
    if (!$validator->process()) {
      throw new ApiException('unauthorized', $resource->validation->meta->id, -1, 401);
    }
    return;
  }

  /**
   * Get the results object.
   * This will create an output class, based on format string, and process through that.
   *
   * @param $format
   * @param $data
   * @param $status
   * @return mixed
   */
  public function getOutputObj($format, $data, $status)
  {
    $class = 'Datagator\\Outputs\\' . ucfirst($this->_cleanData($format));
    return new $class($status, $data);
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
    $request->client = array_shift($args);
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
}
