<?php
/**
 * Class Api.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

use Gaterdata\Db;
use Gaterdata\Resource;
use Spyc;
use Cascade\Cascade;

/**
 * Class Api
 *
 * Process REST requests.
 */
class Api
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ProcessorHelper
     */
    private $helper;

    /**
     * @var boolean
     */
    private $test = false; // false or filename in /yaml/test

    /**
     * @var \ADOConnection
     */
    private $db;

    /**
     * @var Config
     */
    private $settings;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Api constructor.
     *
     * @param array $config Config array.
     *
     * @throws ApiException Exception flowing though.
     */
    public function __construct(array $config)
    {
        $this->settings = $config;
        Cascade::fileConfig($this->settings['debug']);
        $this->logger = Cascade::getLogger('api');
        $this->cache = new Cache($this->settings, $this->logger, $this->settings['api']['cache']);
        $this->helper = new ProcessorHelper();
    }

    /**
     * Process the rest request.
     *
     * @return mixed
     *
     * @throws ApiException Exception flowing though.
     */
    public function process()
    {
        // DB link.
        $dsnOptionsArr = [];
        foreach ($this->settings['db']['options'] as $k => $v) {
            $dsnOptionsArr[] = "$k=$v";
        }
        $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
        $dsn = $this->settings['db']['driver'] . '://'
            . $this->settings['db']['username'] . ':'
            . $this->settings['db']['password'] . '@'
            . $this->settings['db']['host'] . '/'
            . $this->settings['db']['database']
            . $dsnOptions;
        $this->db = \ADONewConnection($dsn);
        if (!$this->db) {
            throw new ApiException('DB connection failed', 2, -1, 500);
        }

        // get the request data for processing.
        $this->request = $this->_getData();
        $this->logger->debug('request: ' . print_r($this->request, true));
        $resource = $this->request->getResource();
        $this->logger->debug('resource: ' . print_r($resource, true));
        $meta = json_decode($resource->getMeta());
        $this->logger->debug('meta: ' . print_r($meta, true));

        // validate user access rights for the call.
        if (!empty($meta->security)) {
            $this->logger->debug('Process security: ' . print_r($meta->security, true));
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
                $this->logger->debug('Process fragment: ' . print_r($fragVal, true));
                $fragments->$fragKey = $this->_crawlMeta($fragVal);
            }
            $this->request->setFragments($fragments);
        }

        // process the call
        $this->logger->debug('Process resource: ' . print_r($meta->process, true));
        $result = $this->_crawlMeta($meta->process);

        // store the results in cache for next time
        if (is_object($result) && get_class($result) == 'Error') {
            $this->logger->notice('Not caching, result is error object');
        } else {
            $cacheData = ['data' => $result];
            $ttl = empty($this->request->getTtl()) ? 0 : $this->request->getTtl();
            $this->cache->set($this->request->getCacheKey(), $cacheData, $ttl);
        }

        return $this->_getOutput($result);
    }

    /**
     * Process the request and request header into a meaningful array object.
     *
     * @return Request
     *
     * @throws ApiException Invalid request or exception flowing though.
     */
    private function _getData()
    {
        $method = $this->_getMethod();
        if ($method == 'options') {
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
        } catch (ApiEception $e) {
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
        $request->setOutFormat($this->getAccept($this->settings['api']['default_format']));
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
     * @param integer $appId Request application ID.
     * @param string $method Request HTTP method.
     * @param array $uriParts Request URI parts.
     *
     * @return array|Db\ApiResource
     *
     * @throws ApiException Exception flowing throuigh, ot invalid test YAML.
     */
    private function _getResource(int $appId, string $method, array $uriParts)
    {
        if (!$this->test) {
            $resourceMapper = new Db\ResourceMapper($this->db);

            $args = [];
            while (sizeof($uriParts) > 0) {
                $uri = implode('/', $uriParts);
                $result = $resourceMapper->findByAppIdMethodUri($appId, $method, $uri);
                if (!empty($result->getResid())) {
                    return [
                    'args' => $args,
                    'resource' => $result,
                    ];
                }
                array_unshift($args, array_pop($uriParts));
            }
            throw new ApiException('invalid request', 3, -1, 404);
        }

        $filepath = $_SERVER['DOCUMENT_ROOT'] . $this->config->__get('dir_yaml') . 'test/' . $this->test;
        if (!file_exists($filepath)) {
            throw new ApiException("invalid test yaml: $filepath", 1, -1, 400);
        }
        $yaml = Spyc::YAMLLoad($filepath);
        $meta = [];
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
     * @param array $uriParts Array of UTI fragments.
     *
     * @return string
     */
    private function _getCacheKey(array $uriParts)
    {
        $cacheKey = $this->_cleanData($this->request->getMethod() . '_' . implode('_', $uriParts));
        $this->logger->info('cache key: ' . $cacheKey);
        return $cacheKey;
    }

    /**
     * Check cache for any results.
     *
     * @param string $cacheKey Cache key.
     *
     * @return boolean
     *
     * @throws ApiException Allow any exceptions to flow through.
     */
    private function _getCache(string $cacheKey)
    {
        if (!$this->cache->cacheActive()) {
            $this->logger->info('not searching for cache - inactive');
            return false;
        }

        $data = $this->cache->get($cacheKey);

        if (!empty($data)) {
            $this->logger->debug('from cache: ' . $data);
            return $this->_getOutput($data, new Request());
        }

        $this->logger->info('no cache entry found');
        return false;
    }

    /**
     * Process the meta data, using depth first iteration.
     *
     * @param mixed $meta The resource metadata.
     *
     * @return mixed
     *
     * @throws ApiException Let any exceptions flow through.
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
            foreach ($node as $value) {
                // $value is a processor and has not been calculated yet, add it to the front of $stack
                if ($this->helper->isProcessor($value) && !isset($results[$value->id])) {
                    if ($processNode) {
                        array_unshift($stack, $node);
                        // We have the first instance of an unprocessed attribute, so re-add $node to the stack
                    }
                    array_unshift($stack, $value);
                    $processNode = false;
                } elseif (is_array($value)) {
                    // $value is an array of values, add to $stack
                    foreach ($value as $item) {
                        if ($this->helper->isProcessor($item) && !isset($results[$item->id])) {
                            if ($processNode) {
                                array_unshift($stack, $node);
                                // We have the first instance of an unprocessed attribute, so re-add $node to the stack
                            }
                            array_unshift($stack, $item);
                            $processNode = false;
                        }
                    }
                } else {
                    // Do nothing, this is a literal.
                }
            }

            // No new attributes have been added to the stack, so we can process the node
            if ($processNode) {
                // traverse through each attribute on the node and place values from $results into $node
                foreach ($node as $key => $value) {
                    if ($this->helper->isProcessor($value)) {
                        // single processor - if value exists in $results,
                        // replace value in $node with value from $results
                        if (isset($results[$value->id])) {
                            $node->{$key} = $results[$value->id];
                            unset($results[$value->id]);
                        }
                    } elseif (is_array($value)) {
                        // array of values - loop through values and if value exists in $results,
                        // replace indexed value in $node with value from $results
                        foreach ($value as $index => $item) {
                            if ($this->helper->isProcessor($item) && isset($results[$item->id])) {
                                $node->{$key}[$index] = $results[$item->id];
                                unset($results[$item->id]);
                            }
                        }
                    }
                }

                $classStr = $this->helper->getProcessorString($node->function);
                $class = new $classStr($node, $this->request, $this->db, Cascade::getLogger('api'));
                $results[$node->id] = $class->process();
            }
        }

        return $results[$finalId];
    }

    /**
     * Get the formatted output.
     *
     * @param mixed $data Data to format.
     * @return mixed
     *
     * @throws ApiException Let any exceptions flow through.
     */
    private function _getOutput($data)
    {
        $result = true;
        $resource = $this->request->getMeta();
        $output = json_decode(json_encode($resource->output), true);

        if (empty($resource->output)) {
            // default to response output if no output defined
            $this->logger->notice('no output section defined - returning the result in the response');
            // translate the output to the correct format as requested in header and return in the response
            $output = ['function' => $this->request->getOutFormat(), 'id' => 'header_defined_output'];
            $result = $this->processOutputResponse($output, $data);
        } else {
            if (Utilities::isAssoc($output)) {
                // Single output defined,
                // translate the output to the correct format as requested in header and return in the response.
                $result = $this->processOutputResponse($output, $data);
            } else {
                // Multiple outputs defined.
                foreach ($output as $index => $outputItem) {
                    if (!isset($outputItem['destination'])) {
                        // Translate the output to the correct format as requested in header and return in the response.
                        $result = $this->processOutputResponse($outputItem, $data, $index);
                    } else {
                        // Process an output item to a remote server..
                        $this->processOutputRemote($outputItem, $data, $index);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Process the output and return in the response.
     *
     * @param array $meta Output metadata.
     * @param mixed $data Response data.
     * @param integer $index Index in the output array.
     *
     * @return mixed
     *
     * @throws ApiException Invalid output processor.
     */
    private function processOutputResponse(array $meta, $data, int $index = null)
    {
        if (!isset($meta['function'])) {
            throw new ApiException("No function found in the output section: $index.", 3, -1, 400);
        }
        $outFormat = ucfirst($this->_cleanData($meta['function']));
        $class = $this->helper->getProcessorString($outFormat, ['Output']);
        $obj = new $class($data, 200, $this->logger);
        $result = $obj->process();
        $obj->setStatus();
        $obj->setHeader();
        return $result;
    }

    /**
     * Process the output.
     *
     * @param array $meta Output mnetadata.
     * @param mixed $data Response data.
     * @param integer $index Index in the output array.
     *
     * @return mixed
     *
     * @throws ApiException Invalid output processor.
     */
    private function processOutputRemote(array $meta, $data, int $index = null)
    {
        if (!isset($meta['function'])) {
            throw new ApiException("No function found in the output section: $index.", 3, -1, 400);
        }
        $outFormat = ucfirst($this->_cleanData($meta['function']));
        $class = $this->helper->getProcessor($outFormat, ['Output']);
        $obj = new $class($data, 200, $meta);
        $obj->process();
    }

    /**
     * Utility function to get the REST method from the $_SERVER var.
     *
     * @return string
     *
     * @throws ApiException Thow exception for unexpected headers.
     */
    private function _getMethod()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($method == 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'delete';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
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
     * @param mixed $default Default value.
     *
     * @return boolean|string
     */
    public function getAccept($default = null)
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
                $values[$key] = [];
                $value = $tempArr[0];
                $values[$key]['weight'] = sizeof($tempArr) == 1 ? 1 : floatval($tempArr[1]);
                $tempArr = explode('/', $value);
                $values[$key]['mimeType'] = $tempArr[0];
                $values[$key]['mimeSubType'] = $tempArr[1];
            }
            usort($values, ['self', '_sortHeadersWeight']);
        }
        if (sizeof($values) < 1) {
            return $default;
        }

        $result = $default;
        switch ($values[0]['mimeType']) {
            case 'image' :
                $result = 'image';
                break;
            case 'text':
            case 'application':
                switch ($values[0]['mimeSubType']) {
                    case '*':
                    case '**':
                        break;
                    default:
                        $result = $values[0]['mimeSubType'];
                        break;
                }
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Custom sort function
     * Sort headers by weight.
     *
     * @param mixed $a Variable a.
     * @param mixed $b Variable b.
     *
     * @return integer
     */
    private static function _sortHeadersWeight($a, $b)
    {
        if ($a['weight'] == $b['weight']) {
            return 0;
        }
        return $a['weight'] > $b['weight'] ? -1 : 1;
    }

    /**
     * Utility recursive function to clean vars for processing.
     *
     * @param mixed $data Variables.
     *
     * @return array|string
     */
    private function _cleanData($data)
    {
        $cleaned = [];
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
