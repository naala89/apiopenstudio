<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

namespace Datagator\Core;
use Datagator\Config;
use Datagator\Processor;
use Datagator\Db;
use Datagator\Security;
use Datagator\Output;
use Datagator\Resource;
use Spyc;

Debug::setup((Config::$debugInterface == 'HTML' ? Debug::HTML : Debug::LOG), Config::$debug, Config::$errorLog);

//When I tasted WCC for the first time is 1985 I knew for the first time I was in love. Never before had a drink made me feel so.
//After my Uncle Bill went to jail in 1986, West Coast Cooler was my friend and got me through a really hard time.
//And now when I taste West Coast Cooler I remember my life and all the good times.

class Api
{
  private $cache;
  private $request;
  private $helper;
  private $test = false; // false or filename in /yaml/test
  private $db;

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
    $this->helper = new ProcessorHelper();
  }

  /**
   * Process the rest request.
   *
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    // set up DB interface
    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('DB connection failed',2 , -1, 500);
    }
    $this->db->debug = Config::$debugDb;

    // get the request data for processing
    $this->request = $this->_getData();
    $resource = $this->request->getResource();

    // validate user for the call, if required
    if (!empty($resource->security)) {
      $this->_crawlMeta($resource->security);
    }

    // fetch the cache of the call, and process into output if it is not stale
    $cacheKey = $this->_getCacheKey($this->request->getUri());
    $result = $this->_getCache($cacheKey);
    if ($result !== false) {
      return $this->_getOutput($result);
    }

    // set fragments in Meta class
    if (isset($resource->fragments)) {
      $fragments = $resource->fragments;
      foreach ($fragments as $fragKey => $fragVal) {
        $fragments->$fragKey = $this->_crawlMeta($fragVal);
      }
      $this->request->setFragments($fragments);
    }

    Debug::variable($this->request, 'request', 3);

    // process the call
    $result = $this->_crawlMeta($resource->process);

    // store the results in cache for next time
    if (is_object($result) && get_class($result) == 'Error') {
      Debug::message('Not caching, result is error object');
    } else {
      $cacheData = array('data' => $result);
      $ttl = empty($this->request->getTtl()) ? 0 : $this->request->getTtl();
      $this->cache->set($cacheKey, $cacheData, $ttl);
    }

    return $this->_getOutput($result);
  }

  /**
   * Process the request and request header into a meaningful array object.
   *
   * @throws \Datagator\Core\ApiException
   */
  private function _getData()
  {
    $method = $this->_getMethod();
    if($method == 'options') {
      die();
    }
    $get = $_GET;
    if (empty($get['request'])) {
      throw new ApiException('invalid request', 3);
    }

    $request = new Request();

    $uriParts = explode('/', trim($get['request'], '/'));
    $appName = array_shift($uriParts);
    $mapper = new Db\ApplicationMapper($this->db);
    $application = $mapper->findByName($appName);
    $appId = $application->getAppId();
    if (empty($appId)) {
      throw new ApiException("invalid application: $appName", 3, -1, 404);
    }
    $request->setAppName($appName);
    $request->setAppId($appId);
    $request->setUri($uriParts);
    $request->setMethod($method);
    $resource = $this->_getResource($appName, $method, $uriParts);
    $resource = json_decode($resource->getMeta());
    $request->setResource($resource);
    $request->setFragments(!empty($resource->fragments) ? $resource->fragments : array());
    $request->setTtl(!empty($resource->ttl) ? $resource->ttl : 0);
    $request->setArgs($uriParts);
    $request->setGetVars(array_diff_assoc($get, array('request' => $get['request'])));
    $request->setPostVars($_POST);
    $request->setIp($_SERVER['REMOTE_ADDR']);
    $request->setOutFormat($this->getAccept(Config::$defaultFormat));

    return $request;
  }

  /**
   * Get the requested resource from the DB.
   * $uriParts will be altered to contain only the values left after the resource is found (i.e. args)
   *
   * @param $appName
   * @param $method
   * @param $uriParts
   * @return \Datagator\Db\Resource
   * @throws \Datagator\Core\ApiException
   */
  private function _getResource($appName, $method, & $uriParts)
  {
    if (!$this->test) {
      $mapper = new Db\ResourceMapper($this->db);
      $args = array();
      $resources = array();

      while (sizeof($resources) < 1 && sizeof($uriParts) > 0) {
        $str = strtolower(implode('/', $uriParts));
        $resources = $mapper->findByAppNamesMethodIdentifier(array('Common', $appName), $method, $str);
        if (sizeof($resources) < 1) {
          array_unshift($args, array_pop($uriParts));
        }
      }
      if (sizeof($resources) < 1) {
        throw new ApiException('resource or client not defined', 3, -1, 404);
      }
      $uriParts = $args;
      return $resources[0];
    }

    $filepath = $_SERVER['DOCUMENT_ROOT'] . Config::$dirYaml . 'test/' . $this->test;
    if (!file_exists($filepath)) {
      throw new ApiException("invalid test yaml: $filepath", 1 , -1, 400);
    }
    $array = Spyc::YAMLLoad($filepath);
    $meta = array();
    $meta['process'] = $array['process'];
    if (!empty($array['security'])) {
      $meta['security'] = $array['security'];
    }
    if (!empty($array['output'])) {
      $meta['output'] = $array['output'];
    }
    if (!empty($array['fragments'])) {
      $meta['fragments'] = $array['fragments'];
    }
    $resource = new Db\Resource();
    $resource->setMeta(json_encode($meta));
    $resource->setTtl($array['ttl']);
    $resource->setMethod($array['method']);
    $resource->setIdentifier(strtolower($array['uri']));
    return $resource;
  }

  /**
   * Get the cache key for a request.
   *
   * @param $uriParts
   * @return string
   */
  private function _getCacheKey($uriParts)
  {
    $cacheKey = $this->_cleanData($this->request->getMethod() . '_' . implode('_', $uriParts));
    Debug::variable($cacheKey, 'cache key', 4);
    return $cacheKey;
  }

  /**
   * Check cache for any results.
   *
   * @param $cacheKey
   * @return bool
   */
  private function _getCache($cacheKey)
  {
    if (!$this->cache->cacheActive()) {
      Debug::message('not searching for cache - inactive', 3);
      return FALSE;
    }

    $data = $this->cache->get($cacheKey);

    if (!empty($data)) {
      Debug::variable($data, 'from cache', 4);
      return $this->_getOutput($data, new Request());
    }

    Debug::message('no cache entry found', 3);
    return FALSE;
  }

  /**
   * Depth first iteration.
   * @param $meta
   */
  private function _crawlMeta($meta)
  {
    if (!$this->helper->isProcessor($meta)) {
      return $meta;
    }

    $finalId = $meta->id;
    $stack = array($meta);
    $results = array();

    while (sizeof($stack) > 0) {

      $node = array_shift($stack);
      $newNodes = array();

      foreach ($node as $key => $value) {
        if ($this->helper->isProcessor($value) && !isset($results[$value->id])) {
          array_unshift($newNodes, $value);
        }
      }

      if (!empty($newNodes)) {
        array_push($newNodes, $node);
      } else {
        foreach ($node as $key => $value) {
          if (isset($results[$value->id])) {
            $node->{$key} = $results[$value->id];
            unset($results[$value->id]);
          }
        }
        $classStr = $this->helper->getProcessorString($node->function);
        $class = new $classStr($node, $this->request);
        $results[$node->id] = $class->process();
      }

      $stack = array_merge($newNodes, $stack);
    }

    return $results[$finalId];
  }

  /**
   * Recursively crawl though metadata. Recurse through Replace all processors with result values and return final value
   * @param $meta
   * @param null $caller
   * @return mixed
   * @throws \Datagator\Core\ApiException
   *
  public function _crawlMeta(& $meta, $caller=null)
  {
    // array of values - parse each one
    if (is_array($meta)) {
      foreach ($meta as $key => $value) {
        $meta[$key] = $this->_crawlMeta($value, $caller);
      }
    }

    // object of value - process each key/value, and process() if a processor
    if (is_object($meta)) {
      // replace each value of key/value pair with final value
      foreach ($meta as $key => & $value) {
        // 1. process all key/value pairs using recursion
        // this allows infinite depth and final first
        $value = $this->_crawlMeta($value, !empty($meta->function) ? $meta->function : null);
      }
      if (!empty($meta->function) && !empty($meta->id)) {
        // 2. process a function
        // this will be arrived at once all values are constants
        if (!empty($caller)) {
          // validate function if limited type allowed
          $callerStr = $this->helper->getProcessorString($caller);
          $class = new $callerStr($meta, $this->request);
          $details = $class->details();
          if (!empty($details['allowedFunctions']) && !in_array($meta->function, $details['allowedFunctions'])) {
            throw new ApiException('invalid function. ' . $meta->function . ' not allowed as input in ' . $caller, 1, $this->id);
          }
        }
        $classStr = $this->helper->getProcessorString($meta->function);
        $class = new $classStr($meta, $this->request);
        return $class->process();
      }
    }

    return $meta;
  }

  /**
   * Get the formatted output.
   *
   * @param $data
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _getOutput($data)
  {
    $result = true;
    $resource = $this->request->getResource();

    // default to response output if no output defined
    if (empty($resource->output)) {
      Debug::message('no output section defined - returning the result in the response');
      // translate the output to the correct format as requested in header and return in the response
      $class = $this->helper->getProcessorString(ucfirst($this->request->getOutFormat()), array('Output'));
      $obj = new $class($data, 200);
      $result = $obj->process();
      $obj->setStatus();
      $obj->setHeader();
    } else {
      foreach ($resource->output as $index => $output) {
        if (is_string($output) && $output == 'response') {
          // translate the output to the correct format as requested in header and return in the response
          $outFormat = ucfirst($this->_cleanData($this->request->outFormat));
          $outFormat = $outFormat == '**' ? 'Json' : $outFormat;
          $class = $this->helper->getProcessor($outFormat, array('Output'));
          $obj = new $class($data, 200);
          $result = $obj->process();
          $obj->setStatus();
          $obj->setHeader();
        } else {
          // treat as a multiple output and let the class take care of the output.
          foreach ($output as $type => $meta) {
            $class = $this->helper->getProcessor($outFormat, array('Output'));
            $obj = new $class($data, 200, $meta);
            $obj->process();
          }
        }
      }
    }

    return $result;
  }

  /**
   * Utility function to get the REST method from the $_SERVER var.
   *
   * @return string
   * @throws \Datagator\Core\ApiException
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
        throw new ApiException("unexpected header", 3, -1, 406);
      }
    }
    return $method;
  }

  /**
   * Calculate a format from string of header Content-Type or Accept.
   *
   * @param $key
   * @param bool|FALSE $default
   * @return bool|string
   */
  public function getAccept($default=null)
  {
    $key = 'accept';
    $headers = getallheaders();
    foreach ($headers as $k => $v) {
      $headers[strtolower($k)] = strtolower($v);
    }
    $header = !empty($headers[strtolower($key)]) ? $headers[strtolower($key)] : '';
    $values = [];
    if (!empty($header)) {
      $values = explode(',', $header);
      foreach ($values as $key => $value) {
        $tempArr = explode(';q=', $value);
        $values[$key] = array();
        $value = $tempArr[0];
        $values[$key]['weight'] = sizeof($tempArr) == 1 ? 1 : floatval($tempArr[1]);
        $tempArr = explode('/', $value);
        $values[$key]['mimeType'] = $tempArr[0];
        $values[$key]['mimeSubType'] = $tempArr[1];
      }
      usort($values, array('self', '_sortHeadersWeight'));
    }
    if (sizeof($values) < 1) {
      return $default;
    }
    $result = '';
    switch ($values[0]['mimeType']) {
      case 'image' :
        return 'image';
      case 'text':
      case 'application':
        return ($result == '*' || $result == '**') ? $default : $values[0]['mimeSubType'];
      default:
        return $default;
    }
    return ($values[0]['mimeSubType'] == '*' || $values[0]['mimeSubType'] == '**') ? $default : $values[0]['mimeSubType'];
  }

  static function _sortHeadersWeight($a, $b)
  {
    if ($a['weight'] == $b['weight']) {
      return 0;
    }
    return $a['weight'] > $b['weight'] ? -1 : 1;
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
