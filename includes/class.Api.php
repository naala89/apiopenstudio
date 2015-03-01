<?php

/**
 * This class processes and routes the rest request.
 * It cleans and stores all arguments, then class the correct class,
 * then calls the process() function on that class
 */

include_once(Config::$dirIncludes . 'class.DB.php');
include_once(Config::$dirIncludes . 'class.Cache.php');
include_once(Config::$dirIncludes . 'class.Error.php');
include_once(Config::$dirIncludes . 'processor/class.Processor.php');

//When I tasted WCC for the first time is 1985 I knew for the first time I was in love. Never before had a drink made me feel so.
//After my Uncle Bill went to jail in 1986, West Coast Cooler was my friend and got me through a really hard time.
//And now when I taste West Coast Cooler I remember my life and all the good times.

  class Api
  {
    private $data;
    private $status;
    private $cache;

    /**
     * Constructor
     *
     * @param mixed $cache
    *    null
    *      set up any cache available to server
    *    string
    *      specify the cache to use
     */
    public function Api($cache=FALSE)
    {
      $this->cache = new Cache($cache);
    }

    /**
     * Process the rest request.
   *
   * @param string $get
   *  the uri in array form
   * @return mixed
   *  the result body
   */
  public function process()
  {
    $requestData = $this->_getData($_GET);
    if (($requestData) === false) {
      $output = $this->_getOutputObj('text', 404, (new Error(-1, 'Invalid request')));
      return $output->process();
    }

    $cacheKey = $this->_cleanData($requestData['method'] . '_' . $requestData['request']);
    Debug::variable($cacheKey, 'cache key', 4);
    // TODO: implement input normalization
    $cacheData = $this->cache->get($cacheKey);
    if (!empty($cacheData)) {
      Debug::variable($cacheData, 'from cache', 4);
      $output = $this->_getOutputObj($requestData['outFormat'], $cacheData['status'], $cacheData['data']);
      return $output->process();
    } else {
      Debug::message('no cache', 4);
    }

    $db = new DB();
    $result = $db->select(array('meta', 'ttl'))
      ->from('resource')
      ->where(array('client', $requestData['client']))
      ->where(array('resource', $requestData['identifier']))
      ->execute();

    if (!$result || $result->num_rows < 1) {
      $error = new Error(-1, 'Resource or client not defined');
      $output = $this->_getOutputObj($requestData['outFormat'], 404, $error);
      $cacheData = array('status' => $output->status, 'data' => $error->process());
      $this->cache->set($cacheKey, $cacheData, 60);
      $result = $output->process();
      return $result;
    } else {
      //$dbObj = new stdClass();
      //$dbObj->ttl = 300;
      //$dbObj->meta = json_encode($this->test6());

      $dbObj = $result->fetch_object();

      Debug::variable($dbObj->meta, 'JSON from DB', 4);
      $processor = new Processor(json_decode($dbObj->meta), $requestData);
      $this->data = $processor->process();
      $this->status = $processor->status;

      $cacheData = array('status' => $this->status, 'data' => $this->data);
      $this->cache->set($cacheKey, $cacheData, $dbObj->ttl);

      $output = $this->_getOutputObj($requestData['outFormat'], $this->status, $this->data);
    }

    return $output->process();
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
      $error = new Error(-1, "Invalid or no output format defined ($format)");
      return $this->_getOutputObj('text', 417, $error);
    }

    include_once($filepath);
    return (new $class($status, $data));
  }

  /**
   * Process the request and request header into a meaningful array object
   *
   * @param array $request
   * @return array
   */
  private function _getData($get)
  {
    if (!$get['request']) {
      return false;
    }

    $requestData = array();
    $requestData['request'] = $get['request'];
    $args = explode('/', trim($requestData['request'], '/'));
    if (sizeof($args) < 2) {
      return false;
    }

    $requestData['method'] = $this->_getMethod();
    $requestData['client'] = array_shift($args);
    $requestData['resource'] = array_shift($args);
    $requestData['action'] = array_shift($args);
    $requestData['identifier'] = $requestData['resource'] . $requestData['action'];
    $requestData['args'] = $args;
    $header = getallheaders();
    $requestData['inFormat'] = $this->_parseType(isset($header['Content-Type']) ? $header['Content-Type'] : NULL);
    $requestData['outFormat'] = $this->_parseType(isset($header['Accept']) ? $header['Accept'] : NULL);

    $requestData['vars'] = array_diff_assoc($get, array('request' => $requestData['request']));
    $requestData['vars'] = $requestData['vars'] + $_POST;
    $body = file_get_contents('php://input');
    if ($requestData['inFormat'] == 'json') {
      $requestData['vars'] = $requestData['vars'] + json_decode($body, TRUE);
    }
    Debug::variable($requestData, 'Request data', 4);

    return $requestData;
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
   * Calculate a format from string of header Content-Type or Accept
   * @param $str
   *    header format type string
   * @return bool|string
   *    format or false on not identified
   */
  private function _parseType($str)
  {
    switch ($str) {
      case 'application/json':
        $result = 'json';
        break;
      case 'application/xml':
        $result = 'xml';
        break;
      case 'application/text':
        $result = 'text';
        break;
      default:
        $result = FALSE;
        break;
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

  /**
   * Test to simulate json for a string
   * @return array
   */
  private function test1()
  {
    return array(
      'type' => 'string',
      'meta' => array(
        'string' => 'hello im static'
      )
    );
  }

  /**
   * Test to simulate json for a string with a substring replacement
   * @return array
   */
  private function test2()
  {
    return array(
      'type' => 'stringTransform',
      'meta' => array(
        'transformType' => 'replace',
        'source' => $this->test1(),
        'data' => array(
          'static' => 'dynamic'
        )
      )
    );
  }

  /**
   * Test to simulate json for a string with a substring replacement into a url input
   * @return array
   */
  private function test3()
  {
    return array(
      'type' => 'inputUrl',
      'meta' => array(
        'method' => 'get',
        'source' => $this->test2(),
        'curlOpt' => array(),
      ),
    );
  }

  /**
   * test a merge of two sources
   */
  private function test4()
  {
    return array(
      'type' => 'merge',
      'meta' => array(
        'mergeType' =>'union',
        'sources' => array(
          $this->test2(),
          $this->test1()
          )
        )
    );
  }

  /**
   * test a filter of drop
   */
  private function test5()
  {
    return array(
      'type' => 'filter',
      'meta' => array(
        'filterType' => 'drop',
        'source' => $this->test4(),
        'data' => array(
          '1'
        )
      )
    );
  }

  /**
   * swellnet dev login
   */
  private function test6()
  {
    return array(
      'type' => 'inputUrl',
      'meta' => array(
        'source' => 'swellnet.local/api/anon/user/login',
        'method' => 'get',
        'auth' => array(
          'type' => 'userpass',
        ),
        'curlOpt' => array(
          'CURLOPT_SSL_VERIFYPEER' => false,
          'CURLOPT_FOLLOWLOCATION' => true
        )
      )
    );
  }
}
