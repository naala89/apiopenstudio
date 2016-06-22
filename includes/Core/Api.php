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
      $this->request->setFragments($resource->fragments);
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
    $get = $_GET;
    if (empty($get['request'])) {
      throw new ApiException('invalid request', 3);
    }
    $method = $this->_getMethod();
    if($method == 'options') {
      die();
    }

    $request = new Request();
    $request->setMethod($method);
    $uriParts = explode('/', trim($get['request'], '/'));
    $appId = array_shift($uriParts);
    $request->setAppId($appId);
    $request->setUri($uriParts);
    $resource = $this->_getResource($appId, $method, $uriParts);
    $resource = json_decode($resource->getMeta());
    $request->setResource($resource);
    $request->setFragments(!empty($resource->fragments) ? $resource->fragments : array());
    $request->setTtl(!empty($resource->ttl) ? $resource->ttl : 0);
    $request->setArgs($uriParts);
    $request->setGetVars(array_diff_assoc($get, array('request' => $get['request'])));
    $request->setPostVars($_POST);
    $request->setIp($_SERVER['REMOTE_ADDR']);
    $header = getallheaders();
    $request->setOutFormat($this->parseType($header, 'Accept', 'json'));

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
        $str = strtolower(implode('', $uriParts));
        $resources = $mapper->findByAppsMethodIdentifier(array('Common', $appName), $method, $str);
        if (sizeof($resources) < 1) {
          array_unshift($args, array_pop($uriParts));
        }
      }
      if (sizeof($resources) < 1) {
        throw new ApiException('resource or client not defined', 3, -1, 404);
      }
      $uriParts = $args;
      if (sizeof($resources) == 1) {
        return $resources[0];
      }

      $mapper = new Db\ApplicationMapper($this->db);
      $application = $mapper->findByName($appName);
      $appId = $application->getAppId();
      foreach ($resources as $resource) {
        if ($resource->getAppId == $appId) {
          return $resource;
        }
      }
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
    $resource->setIdentifier(strtolower($array['uri']['noun']) . strtolower($array['uri']['verb']));
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
   * Recursively crawl though metadata. Recurse through Replace all processors with result values and return final value
   * @param $meta
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function _crawlMeta(& $meta)
  {
    // array of values - parse each one
    if (is_array($meta)) {
      foreach ($meta as $key => & $value) {
        $value = $this->_crawlMeta($value);
      }
    }

    // object of value - process each key/value, and process() if a processpr
    if (is_object($meta)) {
      // replace each value of key/value pair with final value
      foreach ($meta as $key => & $value) {
        $value = $this->_crawlMeta($value);
      }
      if (!empty($meta->function) && !empty($meta->id)) {
        $classStr = $this->getProcessor($meta->function);
        $class = new $classStr($meta, $this->request);
        return $class->process();
      }
    }

    return $meta;
  }

  /**
   * Return processor namespace and class name string.
   * @param $className
   * @param array $namespaces
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  public function getProcessor($className, $namespaces=array('Security', 'Endpoint', 'Output', 'Processor'))
  {
    $className = ucfirst(trim($className));
    $class = null;

    foreach ($namespaces as $namespace) {
      $classStr = "\\Datagator\\$namespace\\$className";
      if (class_exists($classStr)) {
        $class = $classStr;
        break;
      }
    }

    if (!$class) {
      throw new ApiException("unknown function in new resource: $className", 1);
    }
    return $classStr;
  }

  /**
   * Validate whether an object or array is a processor.
   * @param $obj
   * @return bool
   */
  public function isProcessor($obj)
  {
    return (is_object($obj) && !empty($obj->function)) || (is_array($obj) && !empty($obj['function']));
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
      $class = $this->getProcessor(ucfirst($this->request->getOutFormat()), array('Output'));
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
          $class = $this->getProcessor($outFormat, array('Output'));
          $obj = new $class($data, 200);
          $result = $obj->process();
          $obj->setStatus();
          $obj->setHeader();
        } else {
          // treat as a multiple output and let the class take care of the output.
          foreach ($output as $type => $meta) {
            $class = $this->getProcessor($outFormat, array('Output'));
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
   * @param $header
   * @param $key
   * @param bool|FALSE $default
   * @return bool|string
   */
  public function parseType($header, $key, $default=null)
  {
    $key = strtolower($key);
    $result = $default;
    if (!empty($header[strtolower($key)])) {
      $parts = preg_split('/\,|\;/', $header[$key]);
      foreach ($parts as $part) {
        $result = preg_replace("/(application||text)\//i",'',$part);
        $result = trim($result);
        $class = "\\Datagator\\Output\\" . $result;
        if (class_exists($class)) {
          return $result;
        }
      }
    }
    return ($result == '*' || $result == '**') ? $default : $result;
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
