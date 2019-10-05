<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

namespace Gaterdata\Core;

use Gaterdata\Processor;
use Gaterdata\Db;
use Gaterdata\Security;
use Gaterdata\Output;
use Gaterdata\Resource;
use Spyc;

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
  private $settings;

  /**
   * Constructor
   *
   * @param mixed $cache
   *  type of cache to use
   *  @see Cache->setup($type)
   */
  public function __construct($cache=FALSE)
  {
    $this->settings = new Config();
    $this->cache = new Cache($cache);
    $this->helper = new ProcessorHelper();
  }

  /**
   * Process the rest request.
   *
   * @return mixed
   * @throws \Gaterdata\Core\ApiException
   */
  public function process()
  {
    // DB link.
    $dsnOptionsArr = [];
    foreach ($this->settings->__get(['db', 'options']) as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $this->settings->__get(['db', 'driver']) . '://root:'
      . $this->settings->__get(['db', 'root_password']) . '@'
      . $this->settings->__get(['db', 'host']) . '/'
      . $this->settings->__get(['db', 'database']) . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
    if (!$this->db) {
      throw new ApiException('DB connection failed',2 , -1, 500);
    }
    $this->db->debug = $this->settings->__get(['debug', 'debugDb']);

    // get the request data for processing.
    $this->request = $this->_getData();
    Debug::variable($this->request, 'request', 3);
    $resource = $this->request->getResource();
    Debug::variable($resource, 'resource', 3);
    $meta = json_decode($resource->getMeta());
    Debug::variable($meta, 'meta', 3);

    // validate user access rights for the call.
    if (!empty($meta->security)) {
      Debug::variable($meta->security, 'Process security', 3);
      $this->_crawlMeta($meta->security);
    }

    // fetch the cache of the call, and process into output if it is not stale
    $result = $this->_getCache($this->request->getCacheKey());
    if ($result !== false) {
      return $this->_getOutput($result);
    }
    // set fragments in Meta class
    if (isset($meta->fragments)) {
      $fragments = $meta->fragments;
      foreach ($fragments as $fragKey => $fragVal) {
        Debug::variable($fragVal, 'Process fragment', 3);
        $fragments->$fragKey = $this->_crawlMeta($fragVal);
      }
      $this->request->setFragments($fragments);
    }

    // process the call
    Debug::variable($meta->process, 'Process resource', 3);
    $result = $this->_crawlMeta($meta->process);

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
   * @throws \Gaterdata\Core\ApiException
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

    try {
      $uriParts = explode('/', trim($get['request'], '/'));

      $accName = array_shift($uriParts);
      $accMapper = new Db\AccountMapper($this->db);
      $account = $accMapper->findByName($accName);
      if (empty($accId = $account->getAccid())) {
        throw new ApiException("invalid request", 3, -1, 404);
      }

      $appName = array_shift($uriParts);
      $appMapper = new Db\ApplicationMapper($this->db);
      $application = $appMapper->findByAccidAppname($accId, $appName);
      if (empty($appId = $application->getAppid())) {
        throw new ApiException("invalid request", 3, -1, 404);
      }

      $result = $this->_getResource($appId, $method, $uriParts);
    }
    catch (ApiEception $e) {
      throw new ApiException($e->getMessage(), 3 -1, 404);
    }

    $request->setAccName($accName);
    $request->setAccId($accId);
    $request->setAppName($appName);
    $request->setAppId($appId);
    $request->setMethod($method);
    $request->setGetVars(array_diff_assoc($get, ['request' => $get['request']]));
    $request->setPostVars($_POST);
    $request->setFiles($_FILES);
    $request->setIp($_SERVER['REMOTE_ADDR']);
    $request->setOutFormat($this->getAccept($this->settings->__get(['api', 'defaultFormat'])));
    $request->setArgs($result['args']);
    $request->setResource($result['resource']);
    $meta = json_decode($result['resource']->getMeta());
    $request->setMeta($meta);
    $request->setUri($result['resource']->getUri());
    $cacheStr = strtolower($request->getUri());
    $cacheStr = preg_replace('~/~', '_', $cacheStr);
    $cacheStr = implode('_', [$accId, $appId, $cacheStr]);
    $request->setCacheKey($cacheStr);
    $request->setFragments(!empty($meta->fragments) ? $meta->fragments : []);
    $request->setTtl(!empty($meta->ttl) ? $meta->ttl : 0);

    return $request;
  }

  /**
   * Get the requested resource from the DB.
   *
   * @param int $appId
   * @param $method
   * @param $uriParts
   * @return \Gaterdata\Db\Resource
   * @throws \Gaterdata\Core\ApiException
   */
  private function _getResource($appId, $method, $uriParts)
  {
    if (!$this->test) {
      $resourceMapper = new Db\ResourceMapper($this->db);

      while (sizeof($uriParts) > 0) {
        $uri = empty($uri) ? array_shift($uriParts) : ("$uri/" . array_shift($uriParts));
        $result = $resourceMapper->findByAppIdMethodUri($appId, $method, $uri);
        if (!empty($result->getResid())) {
          return [
            'args' => $uriParts,
            'resource' => $result,
          ];
        }
      }
      throw new ApiException('invalid request', 3, -1, 404);
    }

    $filepath = $_SERVER['DOCUMENT_ROOT'] . $this->config->__get('dirYaml') . 'test/' . $this->test;
    if (!file_exists($filepath)) {
      throw new ApiException("invalid test yaml: $filepath", 1 , -1, 400);
    }
    $yaml = Spyc::YAMLLoad($filepath);
    $meta = array();
    $meta['process'] = $yaml['process'];
    if (!empty($yaml['security'])) {
      $meta['security'] = $yaml['security'];
    }
    if (!empty($yaml['output'])) {
      $meta['output'] = $yaml['output'];
    }
    if (!empty($yaml['fragments'])) {
      $meta['fragments'] = $yaml['fragments'];
    }
    $resource = new Db\ApiResource();
    $resource->setMeta(json_encode($meta));
    $resource->setTtl($yaml['ttl']);
    $resource->setMethod($yaml['method']);
    $resource->setIdentifier(strtolower($yaml['uri']));
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
   * Process the meta data, using depth first iteration.
   * 
   * @param $meta
   * @return mixed
   */
  private function _crawlMeta($meta)
  {
    if (!$this->helper->isProcessor($meta)) {
      return $meta;
    }

    $finalId = $meta->id;
    $stack = [$meta];
    $results = [];

    while (sizeof($stack) > 0) {

      $node = array_shift($stack);
      $processNode = true;

      // traverse through each attribute on the node
      foreach ($node as $key => $value) {

        // $value is a processor and has not been calculated yet, add it to the front of $stack
        if ($this->helper->isProcessor($value) && !isset($results[$value->id])) {
          if ($processNode) {
            array_unshift($stack, $node); // We have the first instance of an unprocessed attribute, so re-add $node to the stack
          }
          array_unshift($stack, $value);
          $processNode = false;

          // $value is an array of values, add to $stack
        } elseif (is_array($value)) {
          foreach ($value as $index => $item) {
            if ($this->helper->isProcessor($item) && !isset($results[$item->id])) {
              if ($processNode) {
                array_unshift($stack, $node); // We have the first instance of an unprocessed attribute, so re-add $node to the stack
              }
              array_unshift($stack, $item);
              $processNode = false;
            }
          }

        }
      }

      // No new attributes have been added to the stack, so we can process the node
      if ($processNode) {
        // traverse through each attribute on the node and place values from $results into $node
        foreach ($node as $key => $value) {
          if ($this->helper->isProcessor($value)) {
            // single processor - if value exists in $results, replace value in $node with value from $results
            if (isset($results[$value->id])) {
              $node->{$key} = $results[$value->id];
              unset($results[$value->id]);
            }
          } elseif (is_array($value)) {
            // array of values - loop through values and if value exists in $results, replace indexed value in $node with value from $results
            foreach ($value as $index => $item) {
              if ($this->helper->isProcessor($item) && isset($results[$item->id])) {
                $node->{$key}[$index] = $results[$item->id];
                unset($results[$item->id]);
              }
            }
          }
        }

        $classStr = $this->helper->getProcessorString($node->function);
        $class = new $classStr($node, $this->request, $this->db);
        $results[$node->id] = $class->process();
      }
    }

    return $results[$finalId];
  }

  /**
   * Get the formatted output.
   *
   * @param $data
   * @return bool
   * @throws \Gaterdata\Core\ApiException
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
   * @throws \Gaterdata\Core\ApiException
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
