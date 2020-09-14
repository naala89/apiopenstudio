<?php
/**
 * Class ResourceBase.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core\Config;
use Gaterdata\Core;
use Gaterdata\Core\ProcessorHelper;
use Gaterdata\Db;
use Monolog\Logger;

/**
 * Class ResourceBase
 *
 * Base class for all resource processors.
 */
abstract class ResourceBase extends Core\ProcessorEntity
{
    /**
     * @var ProcessorHelper
     */
    protected $helper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource',
        'machineName' => 'resource_base',
        // phpcs:ignore
        'description' => 'Create, edit or fetch a custom API resource for the application. NOTE: in the case of DELETE, the args for the input should be as GET vars - POST vars are not guaranteed on all servers with this method.',
        'menu' => 'Admin',
        'input' => [
            'method' => [
                'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'delete', 'push'],
                'default' => '',
            ],
            'accName' => [
                'description' => 'The application name that the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'appName' => [
                'description' => 'The application name that the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uri' => [
                // phpcs:ignore
                'description' => 'The URI for the resource, i.e. the part after the App ID in the URL (only used if fetching or deleting a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'resourceString' => [
                // phpcs:ignore
                'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'resourceFile' => [
                // phpcs:ignore
                'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ]
    ];

    /**
     * ResourceBase constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->helper = new ProcessorHelper();
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $accName = $this->val('accName', true);
        $appName = $this->val('appName', true);

        switch ($this->request->getMethod()) {
            case 'post':
                if (empty($accName)) {
                    throw new Core\ApiException('Missing accName', 1, $this->id);
                }
                if (empty($appName)) {
                    throw new Core\ApiException('Missing appName', 1, $this->id);
                }
                $string = $this->val('resourceString', true);
                $resource = $this->_importData($string);
                $result = $this->create($resource, $accName, $appName);
                break;
            case 'get':
                $appId = $this->request->getAppId();
                $method = $this->val('method', true);
                $uri = $this->val('uri', true);
                if (empty($method)) {
                    throw new Core\ApiException('Missing method attribute in new resource', 1, $this->id);
                }
                if (empty($uri)) {
                    throw new Core\ApiException('Missing uri attribute in new resource', 1, $this->id);
                }
                $result = $this->read($appId, $method, $uri);
                break;
            case 'delete':
                $appId = $this->request->getAppId();
                $method = $this->val('method', true);
                $uri = $this->val('uri', true);
                if (empty($method)) {
                    throw new Core\ApiException('Missing method attribute in new resource', 1, $this->id);
                }
                if (empty($uri)) {
                    throw new Core\ApiException('Missing uri attribute in new resource', 1, $this->id);
                }
                $result = $this->delete($appId, $method, $uri);
                break;
            default:
                throw new Core\ApiException('unknown method value in new resource', 3, $this->id);
                break;
        }

        return $result;
    }

    /**
     * Abstract class used to fetch input resource into the correct array format.
     * This has to be declared in each derived class, so that we can cater for many input formats.
     *
     * @param mixed $data Input data.
     *
     * @return mixed
     */
    abstract protected function _importData($data);

    /**
     * Abstract class used to fetch input resource into the correct array format.
     * This has to be declared in each derived class, so that we can cater for many output formats.
     *
     * @param mixed $data Input data.
     *
     * @return mixed
     */
    abstract protected function _exportData($data);

    /**
     * Fetch a resource.
     *
     * @param integer $appId Application ID.
     * @param string $method Resource method.
     * @param string $uri Resource URI.
     *
     * @return mixed
     *
     * @throws Core\ApiException Error.
     */
    protected function read(int $appId, string $method, string $uri)
    {
        if (empty($appId)) {
            throw new Core\ApiException('missing application ID, cannot find resource', 3, $this->id, 400);
        }
        if (empty($method)) {
            throw new Core\ApiException('missing method parameter, cannot find resource', 1, $this->id, 400);
        }
        if (empty($uri)) {
            throw new Core\ApiException('missing $uri parameter, cannot find resource', 1, $this->id, 400);
        }
        $identifier = strtolower($uri);

        $mapper = new Db\ResourceMapper($this->db);
        $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
        if (empty($resource->getId())) {
            throw new Core\ApiException('Resource not found', 1, $this->id, 200);
        }

        $result = json_decode($resource->getMeta(), true);
        $result['uri'] = $resource->getIdentifier();
        $result['name'] = $resource->getName();
        $result['description'] = $resource->getDescription();
        $result['method'] = $resource->getMethod();
        $result['ttl'] = $resource->getTtl();

        return new Core\DataContainer($this->_exportData($result), 'text');
    }

    /**
     * Delete a resource.
     *
     * @param integer $appId Application ID.
     * @param string $method Resource method.
     * @param string $uri Resource URI.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException Error.
     */
    protected function delete(int $appId, string $method, string $uri)
    {
        if (empty($appId)) {
            throw new Core\ApiException('missing application ID, cannot find resource', 3, $this->id, 400);
        }
        if (empty($method)) {
            throw new Core\ApiException('missing method parameter, cannot find resource', 1, $this->id, 400);
        }
        if (empty($uri)) {
            throw new Core\ApiException('missing uri parameter, cannot find resource', 1, $this->id, 400);
        }

        $identifier = strtolower($uri);
        $mapper = new Db\ResourceMapper($this->db);
        $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);

        return new Core\DataContainer($mapper->delete($resource) ? 'true' : 'false', 'text');
    }

    /**
     * Create or update a resource from input data into the caller's app and acc.
     *
     * @param array $data Metadata.
     * @param string $accName Account name.
     * @param string $appName Application name.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException Error.
     */
    protected function create(array $data, string $accName, string $appName)
    {
        $this->logger->debug('New resource' . print_r($data, true));
        $this->_validateData($data);

        $name = $data['name'];
        $description = $data['description'];
        $method = $data['method'];
        $uri = strtolower($data['uri']);
        $meta = [];
        if (!empty($data['security'])) {
            $meta['security'] = $data['security'];
        }
        $meta['process'] = $data['process'];
        if (!empty($data['fragments'])) {
            $meta['fragments'] = $data['fragments'];
        }
        $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

        // Prevent unauthorised editing of admin resources.
        $settings = new Config();
        $coreAccountName = $settings->__get(['api', 'core_account']);
        $coreApplicationName = $settings->__get(['api', 'core_application']);
        $coreResourceLock = $settings->__get(['api', 'core_resource_lock']);
        if ($coreResourceLock && $accName == $coreAccountName && $appName == $coreApplicationName) {
            throw new Core\ApiException("Resources for $coreAccountName/$coreApplicationName are locked", 6, -1, 406);
        }

        $accountMapper = new Db\AccountMapper($this->db);
        $applicationMapper = new Db\ApplicationMapper($this->db);
        $resourceMapper = new Db\ResourceMapper($this->db);
        $account = $accountMapper->findByName($accName);
        $accId = $account->getAccid();
        $application = $applicationMapper->findByAccidAppname($accId, $appName);
        $appId = $application->getAppid();
        $resource = $resourceMapper->findByAppIdMethodUri($appId, $method, $uri);
        $resource->setAppId($appId);
        $resource->setName($name);
        $resource->setDescription($description);
        $resource->setMethod($method);
        $resource->setUri($uri);
        $resource->setMeta(json_encode($meta));
        $resource->setTtl($ttl);

        return new Core\DataContainer($resourceMapper->save($resource) ? 'true' : 'false', 'text');
    }

    /**
     * Validate input data is well formed.
     *
     * @param mixed $data Data to validate.
     *
     * @return void
     *
     * @throws Core\ApiException Error.
     */
    protected function _validateData($data)
    {
        $this->logger->info("Validating the new resource...");
        // check mandatory elements exists in data
        if (empty($data)) {
            throw new Core\ApiException("empty resource uploaded", 6, $this->id, 406);
        }
        if (is_array($data) && sizeof($data) == 1 && $data[0] == $this->meta->resource) {
            $message = 'Form-data element with name: "' . $this->meta->resource . '" not found.';
            throw new Core\ApiException($message, 6, -1, 406);
        }
        if (!isset($data['name'])) {
            throw new Core\ApiException("missing name in new resource", 6, -1, 406);
        }
        if (!isset($data['description'])) {
            throw new Core\ApiException("missing description in new resource", 6, -1, 406);
        }
        if (!isset($data['uri'])) {
            throw new Core\ApiException("missing uri in new resource", 6, -1, 406);
        }
        if (!isset($data['method'])) {
            throw new Core\ApiException("missing method in new resource", 6, -1, 406);
        }
        if (!isset($data['process'])) {
            throw new Core\ApiException("missing process in new resource", 6, -1, 406);
        }
        if (!isset($data['ttl']) || strlen($data['ttl']) < 1) {
            throw new Core\ApiException("missing or negative ttl in new resource", 6, -1, 406);
        }

        // validate for identical IDs
        $this->_identicalIds($data);

        // validate dictionaries
        if (isset($data['security'])) {
            // check for identical IDs
            $this->_validateDetails($data['security']);
        }
        if (!empty($data['output'])) {
            if (!is_array($data['output']) || Core\Utilities::is_assoc($data['output'])) {
                throw new Core\ApiException('invalid output structure in new resource', 6, -1, 406);
            }
            foreach ($data['output'] as $i => $output) {
                if (is_array($output)) {
                    if (!$this->helper->isProcessor($output)) {
                        $message = "bad function declaration in output at index $i in new resource";
                        throw new Core\ApiException($message, 6, -1, 406);
                    }
                    $this->_validateDetails($output);
                } elseif ($output != 'response') {
                    throw new Core\ApiException("invalid output structure at index: $i, in new resource", 6, -1, 406);
                }
            }
        }
        if (!empty($data['fragments'])) {
            if (!Core\Utilities::is_assoc($data['fragments'])) {
                throw new Core\ApiException("invalid fragments structure in new resource", 6, -1, 406);
            }
            foreach ($data['fragments'] as $fragKey => $fragVal) {
                $this->_validateDetails($fragVal);
            }
        }
        $this->_validateDetails($data['process']);
    }

    /**
     * Search for identical IDs.
     *
     * @param array $meta Metadata.
     *
     * @return void
     *
     * @throws \Gaterdata\Core\ApiException Error.
     */
    private function _identicalIds(array $meta)
    {
        $id = [];
        $stack = [$meta];

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                if (in_array($node['id'], $id)) {
                    throw new Core\ApiException('identical ID in new resource: ' . $node['id'], 6, -1, 406);
                }
                $id[] = $node['id'];
            }
            if (is_array($node)) {
                foreach ($node as $item) {
                    array_unshift($stack, $item);
                }
            }
        }
    }

    /**
     * Validate a resource section
     *
     * @param array $meta Metadata.
     *
     * @return void
     *
     * @throws Core\ApiException Error.
     */
    private function _validateDetails(array $meta)
    {
        $stack = array($meta);

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                $classStr = $this->helper->getProcessorString($node['function']);
                $class = new $classStr($meta, new Core\Request(), $this->db);
                $details = $class->details();
                $id = $node['id'];
                $this->logger->info("validating: $id");

                foreach ($details['input'] as $inputKey => $inputDef) {
                    $min = $inputDef['cardinality'][0];
                    $max = $inputDef['cardinality'][1];
                    $literalAllowed = $inputDef['literalAllowed'];
                    $limitFunctions = $inputDef['limitFunctions'];
                    $limitTypes = $inputDef['limitTypes'];
                    $limitValues = $inputDef['limitValues'];
                    $count = 0;

                    if (!empty($node[$inputKey])) {
                        $input = $node[$inputKey];

                        if ($this->helper->isProcessor($input)) {
                            if (!empty($limitFunctions) && !in_array($input['function'], $limitFunctions)) {
                                $message = 'processor ' . $input['id'] . ' is an invalid function type (only "'
                                    . implode('", ', $limitFunctions) . '" allowed)';
                                throw new Core\ApiException($message, 6, $id, 406);
                            }
                            array_unshift($stack, $input);
                            $count = 1;
                        } elseif (is_array($input)) {
                            foreach ($input as $item) {
                                if ($this->helper->isProcessor($item)) {
                                    array_unshift($stack, $item);
                                } else {
                                    $this->_validateTypeValue($item, $limitTypes, $id);
                                }
                            }
                            $count = sizeof($input);
                        } elseif (!$literalAllowed) {
                            $message = "literals not allowed as input for '$inputKey' in function: $id";
                            throw new Core\ApiException($message, 6, $id, 406);
                        } else {
                            if (!empty($limitValues) && !in_array($input, $limitValues)) {
                                $message = "invalid value type for '$inputKey' in function: $id";
                                throw new Core\ApiException($message, 6, $id, 406);
                            }
                            if (!empty($limitTypes)) {
                                $this->_validateTypeValue($input, $limitTypes, $id);
                            }
                            $count = 1;
                        }
                    }

                    // validate cardinality
                    if ($count < $min) {
                        // check for nothing to validate and if that is ok.
                        $message = "input '$inputKey' in function '" . $node['id'] . "' requires min $min";
                        throw new Core\ApiException($message, 6, $id, 406);
                    }
                    if ($max != '*' && $count > $max) {
                        $message = "input '$inputKey' in function '" . $node['id'] . "' requires max $max";
                        throw new Core\ApiException($message, 6, $id, 406);
                    }
                }
            } elseif (is_array($node)) {
                foreach ($node as $key => $value) {
                    array_unshift($stack, $value);
                }
            }
        }
    }

    /**
     * Compare an element type and possible literal value or type in the input resource with the definition in the
     * Processor it refers to. If the element type is processor, recursively iterate through, using the calling
     * function _validateProcessor().
     *
     * @param mixed $element Value to validate.
     * @param array $accepts Array of accepted data types.
     * @param integer $id Processor ID.
     *
     * @return boolean
     *
     * @throws Core\ApiException Error.
     */
    private function _validateTypeValue($element, array $accepts, int $id)
    {
        if (empty($accepts)) {
            return true;
        }
        $valid = false;

        foreach ($accepts as $accept) {
            if ($accept == 'file') {
                $valid = true;
                break;
            } elseif ($accept == 'literal' && (is_string($element) || is_numeric($element))) {
                $valid = true;
                break;
            } elseif ($accept == 'boolean'
                && filter_var($element, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
                $valid = true;
                break;
            } elseif ($accept == 'integer'
                && filter_var($element, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null) {
                $valid = true;
                break;
            } elseif ($accept == 'text' && is_string($element)) {
                $valid = true;
                break;
            } elseif ($accept == 'float'
                && filter_var($element, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null) {
                $valid = true;
                break;
            } elseif ($accept == 'array' && is_array($element)) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            $message = 'invalid literal in new resource (' . print_r($element) . '. only "' .
                implode("', '", $accepts) . '" accepted';
            throw new Core\ApiException($message, 6, $id, 406);
        }
        return $valid;
    }
}
