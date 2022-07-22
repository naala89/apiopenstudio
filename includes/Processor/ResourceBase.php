<?php

/**
 * Class ResourceBase.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\ProcessorHelper;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;

/**
 * Class ResourceBase
 *
 * Base class for all resource processors.
 */
abstract class ResourceBase extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
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
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'delete', 'push'],
                'default' => null,
            ],
            'accName' => [
                'description' => 'The application name that the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'appName' => [
                'description' => 'The application name that the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'uri' => [
                // phpcs:ignore
                'description' => 'The URI for the resource, i.e. the part after the App ID in the URL (only used if fetching or deleting a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'resourceString' => [
                // phpcs:ignore
                'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'resourceFile' => [
                // phpcs:ignore
                'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
        ]
    ];

    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    protected ProcessorHelper $helper;

    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    private AccountMapper $accountMapper;

    /**
     * Config class.
     *
     * @var Config
     */
    private Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->helper = new ProcessorHelper();
        $this->settings = new Config();
        $this->accountMapper = new AccountMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $accName = $this->val('accName', true);
        $appName = $this->val('appName', true);
        $appId = $this->request->getAppId();

        switch ($this->request->getMethod()) {
            case 'post':
                if (empty($accName)) {
                    throw new ApiException('Missing accName', 1, $this->id);
                }
                if (empty($appName)) {
                    throw new ApiException('Missing appName', 1, $this->id);
                }
                $string = $this->val('resourceString', true);
                $resource = $this->importData($string);
                $result = $this->create($resource, $accName, $appName);
                break;
            case 'get':
                $method = $this->val('method', true);
                $uri = $this->val('uri', true);
                if (empty($method)) {
                    throw new ApiException('Missing method attribute in new resource', 1, $this->id);
                }
                if (empty($uri)) {
                    throw new ApiException('Missing uri attribute in new resource', 1, $this->id);
                }
                $result = $this->read($appId, $method, $uri);
                break;
            case 'delete':
                $method = $this->val('method', true);
                $uri = $this->val('uri', true);
                if (empty($method)) {
                    throw new ApiException('Missing method attribute in new resource', 1, $this->id);
                }
                if (empty($uri)) {
                    throw new ApiException('Missing uri attribute in new resource', 1, $this->id);
                }
                $result = $this->delete($appId, $method, $uri);
                break;
            default:
                throw new ApiException('unknown method value in new resource', 3, $this->id);
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
    abstract protected function importData($data);

    /**
     * Abstract class used to fetch input resource into the correct array format.
     * This has to be declared in each derived class, so that we can cater for many output formats.
     *
     * @param mixed $data Input data.
     *
     * @return mixed
     */
    abstract protected function exportData($data);

    /**
     * Fetch a resource.
     *
     * @param integer $appId Application ID.
     * @param string $method Resource method.
     * @param string $uri Resource URI.
     *
     * @return DataContainer
     *
     * @throws ApiException Error.
     */
    protected function read(int $appId, string $method, string $uri): DataContainer
    {
        if (empty($appId)) {
            throw new ApiException('missing application ID, cannot find resource', 3, $this->id, 400);
        }
        if (empty($method)) {
            throw new ApiException('missing method parameter, cannot find resource', 1, $this->id, 400);
        }
        if (empty($uri)) {
            throw new ApiException('missing $uri parameter, cannot find resource', 1, $this->id, 400);
        }
        $uri = strtolower($uri);

        $mapper = new ResourceMapper($this->db, $this->logger);
        $resource = $mapper->findByAppIdMethodUri($appId, $method, $uri);
        if (empty($resource->getResid())) {
            throw new ApiException('Resource not found', 1, $this->id, 200);
        }

        $result = json_decode($resource->getMeta(), true);
        $result['uri'] = $resource->getUri();
        $result['name'] = $resource->getName();
        $result['description'] = $resource->getDescription();
        $result['method'] = $resource->getMethod();
        $result['ttl'] = $resource->getTtl();

        try {
            $result = new DataContainer($this->exportData($result), 'text');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * Delete a resource.
     *
     * @param integer $appId Application ID.
     * @param string $method Resource method.
     * @param string $uri Resource URI.
     *
     * @return DataContainer
     *
     * @throws ApiException Error.
     */
    protected function delete(int $appId, string $method, string $uri): DataContainer
    {
        if (empty($appId)) {
            throw new ApiException('missing application ID, cannot find resource', 3, $this->id, 400);
        }
        if (empty($method)) {
            throw new ApiException('missing method parameter, cannot find resource', 1, $this->id, 400);
        }
        if (empty($uri)) {
            throw new ApiException('missing uri parameter, cannot find resource', 1, $this->id, 400);
        }

        $uri = strtolower($uri);
        $mapper = new ResourceMapper($this->db, $this->logger);
        $resource = $mapper->findByAppIdMethodUri($appId, $method, $uri);

        try {
            $result = new DataContainer($mapper->delete($resource) ? 'true' : 'false', 'text');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * Create or update a resource from input data into the caller's app and acc.
     *
     * @param array $data Metadata.
     * @param string $accName Account name.
     * @param string $appName Application name.
     *
     * @return DataContainer
     *
     * @throws ApiException Error.
     */
    protected function create(array $data, string $accName, string $appName): DataContainer
    {
        $this->logger->debug('api', 'New resource' . print_r($data, true));
        $this->validateData($data);

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
        try {
            $settings = new Config();
            $coreAccountName = $settings->__get(['api', 'core_account']);
            $coreApplicationName = $settings->__get(['api', 'core_application']);
            $coreResourceLock = $settings->__get(['api', 'core_resource_lock']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if ($coreResourceLock && $accName == $coreAccountName && $appName == $coreApplicationName) {
            throw new ApiException("Resources for $coreAccountName/$coreApplicationName are locked", 4, $this->id, 406);
        }

        $accountMapper = new AccountMapper($this->db, $this->logger);
        $applicationMapper = new ApplicationMapper($this->db, $this->logger);
        $resourceMapper = new ResourceMapper($this->db, $this->logger);
        try {
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

            return new DataContainer($resourceMapper->save($resource));
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }

    /**
     * Validate input data is well formed.
     *
     * @param mixed $data Data to validate.
     *
     * @return void
     *
     * @throws ApiException Error.
     */
    protected function validateData($data)
    {
        $this->logger->info('api', 'Validating the new resource...');
        // check mandatory elements exists in data
        if (empty($data)) {
            throw new ApiException("empty resource uploaded", 6, $this->id, 406);
        }
        if (is_array($data) && sizeof($data) == 1 && $data[0] == $this->meta['resource']) {
            $message = 'Form-data element with name: "' . $this->meta['resource'] . '" not found.';
            throw new ApiException($message, 6, $this->id, 406);
        }
        if (!isset($data['name'])) {
            throw new ApiException("missing name in new resource", 6, $this->id, 406);
        }
        if (!isset($data['description'])) {
            throw new ApiException("missing description in new resource", 6, $this->id, 406);
        }
        if (!isset($data['uri'])) {
            throw new ApiException("missing uri in new resource", 6, $this->id, 406);
        }
        if (!isset($data['method'])) {
            throw new ApiException("missing method in new resource", 6, $this->id, 406);
        }
        if (!isset($data['process'])) {
            throw new ApiException("missing process in new resource", 6, $this->id, 406);
        }
        if (!isset($data['ttl']) || strlen($data['ttl']) < 1) {
            throw new ApiException("missing or negative ttl in new resource", 6, $this->id, 406);
        }

        // validate for identical IDs
        $this->identicalIds($data);

        // validate dictionaries
        if (isset($data['security'])) {
            // check for identical IDs
            $this->validateDetails($data['security']);
        }
        if (!empty($data['output'])) {
            if (!is_array($data['output']) || Utilities::isAssoc($data['output'])) {
                throw new ApiException('invalid output structure in new resource', 6, $this->id, 406);
            }
            foreach ($data['output'] as $i => $output) {
                if (is_array($output)) {
                    if (!$this->helper->isProcessor($output)) {
                        $message = "bad processor declaration in output at index $i in new resource";
                        throw new ApiException($message, 6, $this->id, 406);
                    }
                    $this->validateDetails($output);
                } elseif ($output != 'response') {
                    throw new ApiException("invalid output structure at index: $i, in new resource", 6, $this->id, 406);
                }
            }
        }
        if (!empty($data['fragments'])) {
            if (!Utilities::isAssoc($data['fragments'])) {
                throw new ApiException("invalid fragments structure in new resource", 6, $this->id, 406);
            }
            foreach ($data['fragments'] as $fragVal) {
                $this->validateDetails($fragVal);
            }
        }
        $this->validateDetails($data['process']);
    }

    /**
     * Search for identical IDs.
     *
     * @param array $meta Metadata.
     *
     * @return void
     *
     * @throws ApiException Error.
     */
    private function identicalIds(array $meta)
    {
        $id = [];
        $stack = [$meta];

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                if (in_array($node['id'], $id)) {
                    throw new ApiException('identical ID in new resource: ' . $node['id'], 6, $this->id, 406);
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
     * @throws ApiException Error.
     */
    private function validateDetails(array $meta)
    {
        $stack = array($meta);

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                try {
                    $classStr = $this->helper->getProcessorString($node['processor']);
                } catch (ApiException $e) {
                    throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
                }
                $class = new $classStr($meta, new Request(), $this->db);
                $details = $class->details();
                $id = $node['id'];
                $this->logger->info('api', "validating: $id");

                foreach ($details['input'] as $inputKey => $inputDef) {
                    list($min, $max) = $inputDef['cardinality'];
                    $literalAllowed = $inputDef['literalAllowed'];
                    $limitProcessors = $inputDef['limitProcessors'];
                    $limitTypes = $inputDef['limitTypes'];
                    $limitValues = $inputDef['limitValues'];
                    $count = 0;

                    if (!empty($node[$inputKey])) {
                        $input = $node[$inputKey];

                        if ($this->helper->isProcessor($input)) {
                            if (!empty($limitProcessors) && !in_array($input['processor'], $limitProcessors)) {
                                $message = 'processor ' . $input['id'] . ' is an invalid processor type (only "'
                                    . implode('", ', $limitProcessors) . '" allowed)';
                                throw new ApiException($message, 6, $id, 406);
                            }
                            array_unshift($stack, $input);
                            $count = 1;
                        } elseif (is_array($input)) {
                            foreach ($input as $item) {
                                if ($this->helper->isProcessor($item)) {
                                    array_unshift($stack, $item);
                                } else {
                                    $this->validateTypeValue($item, $limitTypes, $id);
                                }
                            }
                            $count = sizeof($input);
                        } elseif (!$literalAllowed) {
                            $message = "literals not allowed as input for '$inputKey' in processor: $id";
                            throw new ApiException($message, 6, $id, 406);
                        } else {
                            if (!empty($limitValues) && !in_array($input, $limitValues)) {
                                $message = "invalid value type for '$inputKey' in processor: $id";
                                throw new ApiException($message, 6, $id, 406);
                            }
                            if (!empty($limitTypes)) {
                                $this->validateTypeValue($input, $limitTypes, $id);
                            }
                            $count = 1;
                        }
                    }

                    // validate cardinality
                    if ($count < $min) {
                        // check for nothing to validate and if that is ok.
                        $message = "input '$inputKey' in processor '" . $node['id'] . "' requires min $min";
                        throw new ApiException($message, 6, $id, 406);
                    }
                    if ($max != '*' && $count > $max) {
                        $message = "input '$inputKey' in processor '" . $node['id'] . "' requires max $max";
                        throw new ApiException($message, 6, $id, 406);
                    }
                }
            } elseif (is_array($node)) {
                foreach ($node as $value) {
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
     * @return void
     *
     * @throws ApiException Error.
     */
    private function validateTypeValue($element, array $accepts, int $id): void
    {
        if (empty($accepts)) {
            return;
        }
        $valid = false;

        foreach ($accepts as $accept) {
            if ($accept == 'file') {
                $valid = true;
                break;
            } elseif ($accept == 'literal' && (is_string($element) || is_numeric($element))) {
                $valid = true;
                break;
            } elseif (
                $accept == 'boolean'
                && filter_var($element, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif (
                $accept == 'integer'
                && filter_var($element, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif ($accept == 'text' && is_string($element)) {
                $valid = true;
                break;
            } elseif (
                $accept == 'float'
                && filter_var($element, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif ($accept == 'array' && is_array($element)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $message = 'invalid literal in new resource (' . print_r($element, true) . '. only "' .
                implode("', '", $accepts) . '" accepted';
            throw new ApiException($message, 6, $id, 406);
        }
    }

    /**
     * Validate application is not core and core not locked.
     *
     * @param Application $application
     *
     * @throws ApiException
     */
    protected function validateCoreProtection(Application $application)
    {
        try {
            $account = $this->accountMapper->findByAccid($application->getAccid());
            $coreAccount = $this->settings->__get(['api', 'core_account']);
            $coreApplication = $this->settings->__get(['api', 'core_application']);
            $coreLock = $this->settings->__get(['api', 'core_resource_lock']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if ($account->getName() == $coreAccount && $application->getName() == $coreApplication && $coreLock) {
            throw new ApiException("Unauthorised: this is a core resource", 4, $this->id, 403);
        }
    }
}
