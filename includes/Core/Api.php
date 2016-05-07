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
  private $test = false; // false or filename in /yaml/test
  private $request;
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

    // disseminate the request for processing
    $get = $_GET;
    $this->request = $this->_getData($get);

    // get the resource & ttl for the processing
    $resource = $this->_getResource($this->request);
    $this->request->resource = json_decode($resource->getMeta());
    $this->request->ttl = $resource->getTtl();

    // validate user for the call, if required
    $this->_getValidation();

    // fetch the cache of the call, if it is not stale
    $data = $this->_getCache();
    if ($data !== false) {
      return $this->_getOutput($data);
    }

    // process the call
    $this->_processFragments();

    Debug::variable($this->request, 'request', 3);
    $processor = new Processor\ProcessorBase($this->request->resource->process, $this->request);
    $data = $processor->process();

    // store the results in cache for next time
    if (is_object($data) && get_class($data) == 'Error') {
      Debug::message('Not caching, result is error object');
    } else {
      $cacheData = array('data' => $data);
      $ttl = empty($this->request->ttl) ? 0 : $this->request->ttl;
      $this->cache->set($this->_getCacheKey(), $cacheData, $ttl);
    }

    return $this->_getOutput($data);
  }

  /**
   * Process the request and request header into a meaningful array object.
   *
   * @param $get
   * @return bool|\stdClass
   * @throws \Datagator\Core\ApiException
   */
  private function _getData($get)
  {
    if (empty($get['request'])) {
      return FALSE;
    }

    $result = new \stdClass();
    $result->request = $get['request'];
    $args = explode('/', trim($result->request, '/'));
    if (sizeof($args) < 2) {
      // need at least noun and verb
      throw new ApiException('invalid request', 3);
    }

    //get request method
    $result->ip = $_SERVER['REMOTE_ADDR'];
    $result->method = $this->_getMethod();
    if($result->method == 'options') {
      // this is a preflight request - respond with 200 and empty payload
      die();
    }

    $result->appId = array_shift($args);
    $result->noun = array_shift($args);
    $result->verb = array_shift($args);
    $result->identifier = $result->noun . $result->verb;
    $result->args = $args;
    $header = getallheaders();
    $result->outFormat = $this->parseType($header, 'Accept', 'json');
    $result->vars = array_diff_assoc($get, array('request' => $result->request));
    $result->vars = $result->vars + $_POST;

    return $result;
  }

  /**
   * Get the requested resource from the DB.
   *
   * @param $request
   * @return \Datagator\Db\Resource
   * @throws \Datagator\Core\ApiException
   */
  private function _getResource($request)
  {
    $mapper = new Db\ResourceMapper($this->db);

    if (!$this->test) {
      $resource = $mapper->findByAppIdMethodIdentifier($request->appId, $request->method, $request->identifier);

      if ($resource->getId() === NULL) {
        throw new ApiException('resource or client not defined', 3, -1, 404);
      }
    } else {
      $filepath = $_SERVER['DOCUMENT_ROOT'] . Config::$dirYaml . 'test/' . $this->test;
      if (!file_exists($filepath)) {
        throw new ApiException("invalid test yaml: $filepath", 1 , -1, 400);
      }
      $array = Spyc::YAMLLoad($filepath);
      $resource = new Db\Resource();
      $meta = new \stdClass();
      $meta->process = $this->_arrayToObject($array['process']);
      if (!empty($array['security'])) {
        $meta->security = $this->_arrayToObject($array['security']);
      }
      if (!empty($array['output'])) {
        $meta->output = $this->_arrayToObject($array['output']);
      }
      if (!empty($array['fragments'])) {
        $meta->fragments = $this->_arrayToObject($array['fragments']);
      }
      $resource->setMeta($meta);
      $resource->setTtl($array['ttl']);
      $resource->setMethod($array['method']);
      $resource->setIdentifier(strtolower($array['uri']['noun']) . strtolower($array['uri']['verb']));
    }

    return $resource;
  }

  /**
   * Perform api request auth if defined in the meta.
   *
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _getValidation()
  {
    if (empty($this->request->resource->security)) {
      return true;
    }
    $class = '\\Datagator\\Security\\' . ucfirst($this->_cleanData($this->request->resource->security->processor));
    $security = new $class($this->request->resource->security->meta, $this->request);
    return $security->process();
  }

  /**
   * Check cache for any results.
   *
   * @return bool
   */
  private function _getCache()
  {
    if (!$this->cache->cacheActive()) {
      Debug::message('not searching for cache - inactive', 3);
      return FALSE;
    }

    $cacheKey = $this->_getCacheKey();
    Debug::variable($cacheKey, 'cache key', 4);
    $data = $this->cache->get($cacheKey);

    if (!empty($data)) {
      Debug::variable($data, 'from cache', 4);
      return $this->_getOutput($data);
    }

    Debug::message('no cache entry found', 3);
    return FALSE;
  }

  /**
   * Get the cache key for a request.
   *
   * @return array|string
   */
  private function _getCacheKey()
  {
    return $this->_cleanData($this->request->method . '_' . $this->request->request);
  }

  /**
   * Process the fragments meta and store the results in $this->request->fragments.
   *
   * @throws \Datagator\Core\ApiException
   */
  private function _processFragments() {
    if (empty($this->request->resource->fragments)) {
      return;
    }

    $this->request->fragments = new \stdClass();

    foreach ($this->request->resource->fragments as $fragment) {
      if (empty($fragment->fragment) || !isset($fragment->meta)) {
        throw new ApiException('bad fragment definition');
      }
      if (is_string($fragment->meta)) {
        // fragment is a constant
        $this->request->fragments->{$fragment->fragment} = $fragment->meta;
      } elseif (!empty($fragment->meta->processor) && !empty($fragment->meta->meta)) {
        // fragment is a processor
        $class = '\\Datagator\\Processor\\' . ucfirst(trim($fragment->meta->processor));
        if (!class_exists($class)) {
          $class = '\\Datagator\\Endpoint\\' . ucfirst(trim($fragment->meta->processor));
          if (!class_exists($class)) {
            $class = '\\Datagator\\Output\\' . ucfirst(trim($fragment->meta->processor));
            if (!class_exists($class)) {
              $class = '\\Datagator\\Security\\' . ucfirst(trim($fragment->meta->processor));
              if (!class_exists($class)) {
                throw new ApiException('unknown processor in fragment: ' . ucfirst(trim($fragment->meta->processor)), 1);
              }
            }
          }
        }
        $processor = new $class($fragment->meta->meta, $this->request);
        $this->request->fragments->{$fragment->fragment} = $processor->process();
      } else {
        throw new ApiException('invalid fragment meta',1);
      }
    }
  }

  /**
   * Get the formatted output.
   *
   * @param $data
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  private function _getOutput($data)
  {
    $result = true;

    // default to response output if no output defined
    if (empty($this->request->resource->output)) {
      Debug::message('no output section defined - returning the result in the response');
      // translate the output to the correct format as requested in header and return in the response
      $outFormat = ucfirst($this->_cleanData($this->request->outFormat));
      $outFormat = $outFormat == '**' ? 'Json' : $outFormat;
      $class = '\\Datagator\\Output\\' . $outFormat;
      if (!class_exists($class)) {
        throw new ApiException('output processor undefined: ' . $outFormat, 1);
      }
      $obj = new $class($data, 200);
      $result = $obj->process();
      $obj->setStatus();
      $obj->setHeader();
    } else {
      foreach ($this->request->resource->output as $index => $output) {
        if (is_string($output) && $output == 'response') {
          // translate the output to the correct format as requested in header and return in the response
          $outFormat = ucfirst($this->_cleanData($this->request->outFormat));
          $outFormat = $outFormat == '**' ? 'Json' : $outFormat;
          $class = '\\Datagator\\Output\\' . $outFormat;
          if (!class_exists($class)) {
            throw new ApiException('output processor undefined: ' . $outFormat, 1);
          }
          $obj = new $class($data, 200);
          $result = $obj->process();
          $obj->setStatus();
          $obj->setHeader();
        } else {
          // treat as a multiple output and let the class take care of the output.
          foreach ($output as $type => $meta) {
            $class = '\\Datagator\\Output\\' . $type;
            if (!class_exists($class)) {
              throw new ApiException('output processor undefined: ' . $type, 1);
            }
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

  private function _arrayToObject($array)
  {
    $json = json_encode($array);
    $object = json_decode($json);
    return $object;
  }
}
