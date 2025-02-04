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
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;

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
     * Config array.
     *
     * @var array
     */
    private array $settings;

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
     *
     * @throws ApiException
     */
    public function __construct(array $config)
    {
        $this->settings = $config;
        $this->logger = new MonologWrapper($config['debug']);
        $this->cache = new Cache($this->settings['api']['cache'], $this->logger);
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
        try {
            $this->db = Utilities::getDbConnection($this->settings['db']);
        } catch (ApiException $e) {
            $this->logger->error('db', $e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode(), $e->getProcessor(), $e->getHtmlCode());
        }

        // get the request data for processing.
        $this->request = $this->getData();
        $this->logger->info('api', 'request: ' . print_r($this->request, true));
        $resource = $this->request->getResource();
        $this->logger->info('api', 'resource: ' . print_r($resource, true));
        $meta = json_decode($resource->getMeta(), true);
        $this->logger->debug('api', 'meta: ' . print_r($meta, true));

        $parser = new TreeParser($this->request, $this->db, $this->logger, $this->cache);

        // validate user access rights for the call.
        if (!empty($meta['security'])) {
            $this->logger->debug('api', 'Process security: ' . print_r($meta['security'], true));
            $parser->pushToProcessingStack($meta['security']);
            $parser->crawlMeta();
        }

        // fetch the cache of the call, and process into output if it is not stale
        $resourceCacheKey = $this->cache->getResourceCacheKey($resource->getResid());
        if (!is_null($result = $this->cache->get($resourceCacheKey))) {
            $this->logger->info('api', 'Returning cached resource result.');
            return $this->getOutput($result, $meta);
        }

        // set fragments in Meta class
        if (isset($meta['fragments'])) {
            foreach ($meta['fragments'] as $fragKey => $fragMeta) {
                $this->logger->debug('api', 'Process fragment: ' . print_r($fragMeta, true));
                $parser->pushToProcessingStack($fragMeta);
                $this->request->setFragment($fragKey, $parser->crawlMeta());
            }
            $parser->setRequest($this->request);
        }

        // process the call
        $this->logger->debug('api', 'Process resource: ' . print_r($meta['process'], true));
        if (!$this->helper->isProcessor($meta['process'])) {
            $result = new DataContainer($meta['process']);
        } else {
            $parser->pushToProcessingStack($meta['process']);
            $result = $parser->crawlMeta();
        }
        $this->logger->debug('api', 'Results: ' . print_r($result, true));

        // store the results in cache for next time
        if (is_object($result) && get_class($result) == 'Error') {
            $this->logger->debug('api', 'Not caching, result is error object');
        } else {
            $ttl = empty($this->request->getTtl()) ? 0 : $this->request->getTtl();
            $this->logger->debug(
                'api',
                "Attempting to cache final result key (ttl): $resourceCacheKey ($ttl)"
            );
            $this->cache->set($resourceCacheKey, $result, $ttl);
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

        // In some cases (like Traefik docker-dev), the first get param is still attached to the request param.
        // We can safely remove this for safety.
        // @TODO: This is a hacky workaround for docker_dev, and should be resolved in that codebase.
        $get['request'] = explode('?', $get['request'])[0];

        $uriParts = explode('/', trim($get['request'], '/'));

        $accName = array_shift($uriParts);
        $accMapper = new AccountMapper($this->db, $this->logger);
        $account = $accMapper->findByName($accName);
        if (empty($accId = $account->getAccid())) {
            throw new ApiException('invalid request', 3, 'oops', 404);
        }

        $appName = array_shift($uriParts);
        $appMapper = new ApplicationMapper($this->db, $this->logger);
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
        $meta = json_decode($result['resource']->getMeta(), true);
        $request->setMeta($meta);
        $request->setUri($result['resource']->getUri());
        $request->setFragments(!empty($meta['fragments']) ? $meta['fragments'] : []);
        $request->setTtl($result['resource']->getTtl());

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
        $resourceMapper = new ResourceMapper($this->db, $this->logger);

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
     * Get the formatted output.
     *
     * @param mixed $data Data to format.
     * @param array $meta Resource metadata.
     *
     * @return mixed
     *
     * @throws ApiException
     */
    private function getOutput($data, array $meta)
    {
        $result = null;

        if (!isset($meta['output'])) {
            // Default response output if no output defined.
            $this->logger->notice('api', 'No output section defined - returning the result in the response');
            $outputs = ['response'];
        } else {
            // Test for single output defined.
            $outputs = Utilities::isAssoc($meta['output']) ? [$meta['output']] : $meta['output'];
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
    private function processOutputResponse(array $meta, $data, int $status = 1, int $index = -1)
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
     * @param array $meta Output metadata.
     * @param mixed $data Response data.
     * @param int|null $index Index in the output array.
     *
     * @return mixed
     *
     * @throws ApiException Invalid output processor.
     */
    private function processOutputRemote(array $meta, $data, int $index = null)
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
     * @throws ApiException Throw exception for unexpected headers.
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
     * @param ?string $default Default value.
     *
     * @return array
     */
    public function getAccept(?string $default = null): array
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
