<?php

/**
 * Class Api.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use ADOConnection;
use ApiOpenStudio\Db;
use stdClass;

/**
 * Class Api
 *
 * Process REST requests.
 */
class Api
{
    /**
     * Cache class.
     *
     * @var Cache
     */
    private Cache $cache;

    /**
     * Request object class.
     *
     * @var Request
     */
    private Request $request;

    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    private ProcessorHelper $helper;

    /**
     * DB connection object.
     *
     * @var ADOConnection
     */
    private ADOConnection $db;

    /**
     * Config class.
     *
     * @var Config
     */
    private $settings;

    /**
     * Logging class.
     *
     * @var MonologWrapper $logger
     */
    private MonologWrapper $logger;

    /**
     * Api constructor.
     *
     * @param array $config Config array.
     * @throws ApiException
     */
    public function __construct(array $config)
    {
        $this->settings = $config;
        $this->logger = new MonologWrapper($config['debug']);
        $this->cache = new Cache($this->settings, $this->logger, $this->settings['api']['cache']);
        $this->helper = new ProcessorHelper();
    }

    /**
     * Process the rest request.
     *
     * @return DataContainer
     *
     * @throws ApiException Exception flowing though.
     */
    public function process(): DataContainer
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
        $conn = ADONewConnection($dsn);
        if (empty($conn)) {
            $this->logger->error('db', 'DB connection failed');
            throw new ApiException('DB connection failed', 2, 'oops', 500);
        }
        $this->db = $conn;

        // get the request data for processing.
        $this->request = $this->getData();
        $this->logger->info('api', 'request: ' . print_r($this->request, true));
        $resource = $this->request->getResource();
        $this->logger->info('api', 'resource: ' . print_r($resource, true));
        $meta = json_decode($resource->getMeta());
        $this->logger->debug('api', 'meta: ' . print_r($meta, true));

        $parser = new TreeParser($this->request, $this->db, $this->logger);

        // validate user access rights for the call.
        if (!empty($meta->security)) {
            $this->logger->debug('api', 'Process security: ' . print_r($meta->security, true));
            $parser->pushToProcessingStack($meta->security);
            $parser->crawlMeta();
        }

        // fetch the cache of the call, and process into output if it is not stale
        $result = $this->getCache($this->request->getCacheKey());
        if ($result !== false) {
            $this->logger->info('api', 'Returning cached results');
            return $this->getOutput(true, $meta);
        }
        // set fragments in Meta class
        if (isset($meta->fragments)) {
            $fragments = $meta->fragments;
            foreach ($fragments as $fragKey => $fragVal) {
                $this->logger->debug('api', 'Process fragment: ' . print_r($fragVal, true));
                $parser->pushToProcessingStack($fragVal);
                $fragments->$fragKey = $parser->crawlMeta();
            }
            $this->request->setFragments($fragments);
        }

        // process the call
        $this->logger->debug('api', 'Process resource: ' . print_r($meta->process, true));
        $parser->pushToProcessingStack($meta->process);
        $result = $parser->crawlMeta();
        $this->logger->debug('api', 'Results: ' . print_r($result, true));


        // store the results in cache for next time
        if (is_object($result) && get_class($result) == 'Error') {
            $this->logger->notice('api', 'Not caching, result is error object');
        } else {
            $cacheData = ['data' => $result];
            $ttl = empty($this->request->getTtl()) ? 0 : $this->request->getTtl();
            $this->cache->set($this->request->getCacheKey(), $cacheData, $ttl);
        }

        return $this->getOutput($result, $meta);
    }

    /**
     * Process the request and request header into a meaningful array object.
     *
     * @return Request
     *
     * @throws ApiException Invalid request or exception flowing though.
     */
    private function getData(): Request
    {
        $method = $this->getMethod();
        if ($method == 'options') {
            die();
        }
        $get = $_GET;
        if (empty($get['request'])) {
            throw new ApiException('invalid request', 3, 'oops', 404);
        }

        $request = new Request();

        $uriParts = explode('/', trim($get['request'], '/'));

        $accName = array_shift($uriParts);
        $accMapper = new Db\AccountMapper($this->db, $this->logger);
        $account = $accMapper->findByName($accName);
        if (empty($accId = $account->getAccid())) {
            throw new ApiException('invalid request', 3, 'oops', 404);
        }

        $appName = array_shift($uriParts);
        $appMapper = new Db\ApplicationMapper($this->db, $this->logger);
        $application = $appMapper->findByAccidAppname($accId, $appName);
        if (empty($appId = $application->getAppid())) {
            throw new ApiException('invalid request', 3, 'oops', 404);
        }

        $result = $this->getResource($appId, $method, $uriParts);

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
     * @return array
     *
     * @throws ApiException Exception flowing through, ot invalid test YAML.
     */
    private function getResource(int $appId, string $method, array $uriParts): array
    {
        $resourceMapper = new Db\ResourceMapper($this->db, $this->logger);

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

        throw new ApiException('invalid request', 3, 'oops', 404);
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
    private function getCache(string $cacheKey): bool
    {
        if (!$this->cache->cacheActive()) {
            $this->logger->info('api', 'not searching for cache - inactive');
            return false;
        }

        $data = $this->cache->get($cacheKey);

        if (!empty($data)) {
            $this->logger->debug('api', 'from cache: ' . $data);
            return $this->getOutput($data);
        }

        $this->logger->info('api', 'no cache entry found');
        return false;
    }

    /**
     * Get the formatted output.
     *
     * @param mixed $data Data to format.
     * @param mixed $meta Data to format.
     *
     * @return mixed
     *
     * @throws ApiException
     */
    private function getOutput($data, $meta)
    {
        $result = null;

        if (!isset($meta->output)) {
            // Default response output if no output defined.
            $this->logger->notice('api', 'no output section defined - returning the result in the response');
            $outputs = ['response'];
        } else {
            // Test for single output defined.
            $outputs = Utilities::isAssoc($meta->output) ? [$meta->output] : $meta->output;
        }

        foreach ($outputs as $index => $output) {
            if ($output == 'response') {
                // Output format is response, so set the output format from the request header.
                $output = [
                    'processor' => $this->request->getOutFormat()['mimeType'],
                    'id' => 'header defined output',
                ];
                // Convert the output to the correct format to return it in the response.
                $result = $this->processOutputResponse($output, $data, 200, $index);
            } else {
                // Process an output item.
                $outputRemoteResult = $this->processOutputRemote($output, $data, $index);
                if (empty($result)) {
                    $output = [
                        'processor' => $this->request->getOutFormat()['mimeType'],
                        'id' => 'header defined output',
                    ];
                    $result = $this->processOutputResponse($output, $outputRemoteResult, 200, $index);
                }
            }
        }

        return $result;
    }

    /**
     * Process the output and return it in the response.
     *
     * @param array $meta Output metadata.
     * @param mixed $data Response data.
     * @param int $status ApiOpenStudio result status code.
     * @param int $index Index in the output array.
     *
     * @return mixed
     *
     * @throws ApiException
     */
    private function processOutputResponse(array $meta, $data, int $status, int $index = -1)
    {
        if (!isset($meta['processor'])) {
            throw new ApiException("No processor found in the output section: $index.", 1, 'oops', 500);
        }

        $outFormat = ucfirst($this->cleanData($meta['processor']));
        $class = $this->helper->getProcessorString($outFormat, ['Output']);
        $obj = new $class($meta, $this->request, $this->logger, $data, $status);

        return $obj->process();
    }

    /**
     * Process the output.
     *
     * @param stdClass $meta Output metadata.
     * @param mixed $data Response data.
     * @param int|null $index Index in the output array.
     *
     * @return mixed
     *
     * @throws ApiException Invalid output processor.
     */
    private function processOutputRemote(stdClass $meta, $data, int $index = null)
    {
        if (!isset($meta->processor)) {
            throw new ApiException("No processor found in the output section: $index.", 1, 'oops', 500);
        }

        $outFormat = ucfirst($this->cleanData($meta->processor));
        $class = $this->helper->getProcessorString($outFormat, ['Output']);
        $obj = new $class($meta, $this->request, $this->logger, $data);

        return $obj->process();
    }

    /**
     * Utility function to get the REST method from the $_SERVER var.
     *
     * @return string
     *
     * @throws ApiException Thow exception for unexpected headers.
     */
    private function getMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($method == 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'delete';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $method = 'put';
            } else {
                throw new ApiException("unexpected header", 3, 'oops', 400);
            }
        }
        return $method;
    }

    /**
     * Calculate a format from string of header Content-Type or Accept.
     *
     * This is in the format [mimeType, mimeSubType] for use internally.
     *
     * example:
     *   ['mimeType' => 'image', 'mimeSubType' => 'jpeg']
     *   ['mimeType' => 'image', 'mimeSubType' => 'png']
     *   ['mimeType' => 'json', 'mimeSubType' => '']
     *   ['mimeType' => 'xml', 'mimeSubType' => '']
     *   ['mimeType' => 'text', 'mimeSubType' => '']
     *   ['mimeType' => 'html', 'mimeSubType' => '']
     *
     * @param string|null $default Default value.
     *
     * @return array
     */
    public function getAccept(string $default = null): array
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
            usort($values, ['self', 'sortHeadersWeight']);
        }

        $result = ['mimeType' => $default, 'mimeSubType' => ''];
        if (sizeof($values) < 1) {
            return $result;
        }

        switch ($values[0]['mimeType']) {
            case 'image':
                $result['mimeType'] = 'image';
                $result['mimeSubType'] = $values[0]['mimeSubType'];
                break;
            case 'text':
            case 'application':
                switch ($values[0]['mimeSubType']) {
                    case '*':
                    case '**':
                        break;
                    default:
                        $result['mimeType'] = $values[0]['mimeSubType'];
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
    private static function sortHeadersWeight($a, $b): int
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
    private function cleanData($data)
    {
        $cleaned = [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $cleaned[$k] = $this->cleanData($v);
            }
        } else {
            $cleaned = trim(strip_tags($data));
        }
        return $cleaned;
    }
}
