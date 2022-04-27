<?php

/**
 * Class ResourceImport.
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
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\ResourceValidator;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ReflectionException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ResourceImport
 *
 * Processor class to import a resource
 */
class ResourceImport extends ProcessorEntity
{
    /**
     * Required keys in a resource yaml file.
     *
     * @var string[]
     */
    private array $requiredKeys = [
        'name',
        'description',
        'uri',
        'method',
        'appid',
        'ttl',
    ];

    /**
     * Config object.
     *
     * @var Config
     */
    private Config $settings;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private UserRoleMapper $userRoleMapper;

    /**
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    private ResourceMapper $resourceMapper;

    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    private AccountMapper $accountMapper;

    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    private ApplicationMapper $applicationMapper;

    /**
     * Resource validator class.
     *
     * @var ResourceValidator
     */
    private ResourceValidator $validator;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Resource import',
        'machineName' => 'resource_import',
        'description' => 'Import a resource from a file.',
        'menu' => 'Admin',
        'input' => [
            'resource' => [
                'description' => 'The resource file file. This can be YAML or JSON.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => ['var_file'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ResourceImport constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
        $this->validator = new ResourceValidator($db, $logger);
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

        $uid = Utilities::getUidFromToken();
        $resource = $this->val('resource');

        // Extract the file contents.
        $fileContents = $resource->getType() == 'file' ? file_get_contents($resource->getData()) : $resource->getData();
        $resource = $this->extractNewResource($fileContents);
        $this->logger->debug('api', 'Decoded new resource: ' . print_r($resource, true));

        $this->validateImportPermissions($uid, $resource);
        $this->validateNewResource($resource);

        // Validate the metadata.
        try {
            $this->validator->validate($resource['meta']);
        } catch (ApiException | ReflectionException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id, 400);
        }

        // Create the final Resource object for saving into the DB.
        $resourceObj = new Resource(
            null,
            $resource['appid'],
            $resource['name'],
            $resource['description'],
            $resource['method'],
            $resource['uri'],
            json_encode($resource['meta'], JSON_UNESCAPED_SLASHES),
            '',
            $resource['ttl']
        );

        // OpenApi
        if (!empty($resource['openapi'])) {
            $resourceObj->setOpenapi(json_encode($resource['openapi'], true));
        } else {
            // Generate default OpenApi fragment.
            $settings = new Config();
            $openApiPathClassName = Utilities::getOpenApiPathClassPath($settings);
            $openApiPathClass = new $openApiPathClassName();

            $openApiPathClass->setDefault($resourceObj);
            $resourceObj->setOpenapi($openApiPathClass->export());
        }

        try {
            $result = !$this->resourceMapper->save($resourceObj);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!$result) {
            return new DataContainer(false, 'boolean');
        }

        $resourceObj = $this->resourceMapper->findByAppIdMethodUri(
            $resourceObj->getAppId(),
            $resourceObj->getMethod(),
            $resourceObj->getUri()
        );
        $resource = $resourceObj->dump();
        $resource['meta'] = json_decode($resource['meta'], true);
        $resource['openapi'] = json_decode($resource['openapi'], true);
        return new DataContainer($resource, 'array');
    }

    /**
     * Validate user permissions to import a resource.
     *
     * @param int $uid
     * @param array $resource
     *
     * @throws ApiException
     */
    protected function validateImportPermissions(int $uid, array $resource)
    {
        if (!isset($resource['appid'])) {
            throw new ApiException(
                'invalid resource, missing appid',
                4,
                $this->id,
                401
            );
        }
        $roles = Utilities::getRolesFromToken();
        // Only developer role permitted to upload resource.
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['role_name'] == 'Developer' && $role['appid'] == $resource['appid']) {
                $permitted = true;
            }
        }
        if (!$permitted) {
            $this->logger->error('api', "Unauthorised resource import. uid: $uid, appid: " . $resource['appid']);
            throw new ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }
    }

    /**
     * Extract the input YAML or JSON into an array.
     *
     * @param string $string
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function extractNewResource(string $string): array
    {
        // attempt string extraction as JSON.
        $resource = json_decode($string, true);

        // attempt string extraction as YAML.
        if ($resource === null) {
            $message = 'unable to parse input as JSON';
            try {
                $resource = Yaml::parse($string);
            } catch (ParseException $exception) {
                $message .= '. Unable to parse the YAML string: ' . $exception->getMessage();
                throw new ApiException(
                    $message,
                    6,
                    $this->id,
                    400
                );
            }
        }

        // Merge the sections into final metadata.
        $resource['meta'] = [];
        if (isset($resource['security'])) {
            $resource['meta'] = array_merge($resource['meta'], ['security' => $resource['security']]);
            unset($resource['security']);
        }
        if (isset($resource['process'])) {
            $resource['meta'] = array_merge($resource['meta'], ['process' => $resource['process']]);
            unset($resource['process']);
        }
        if (isset($resource['output'])) {
            $resource['meta'] = array_merge($resource['meta'], ['output' => $resource['output']]);
            unset($resource['output']);
        }

        return $resource;
    }

    /**
     * Validate the new resource array.
     *
     * @param array $resource
     *
     * @throws ApiException
     */
    protected function validateNewResource(array $resource)
    {
        // Validate required keys in the imported file.
        foreach ($this->requiredKeys as $requiredKey) {
            if (!isset($resource[$requiredKey])) {
                $this->logger->error('api', "Missing $requiredKey in new resource");
                throw new ApiException("Missing $requiredKey in new resource", 6, $this->id, 400);
            }
        }

        // Validate TTL in the imported file.
        if ($resource['ttl'] < 0) {
            $this->logger->error('api', 'Negative ttl in new resource');
            throw new ApiException("Negative ttl in new resource", 6, $this->id, 400);
        }

        // Validate the application exists.
        $application = $this->applicationMapper->findByAppid($resource['appid']);
        if (empty($application)) {
            $this->logger->error('api', 'Invalid application: ' . $resource['appid']);
            throw new ApiException(
                'Invalid application: ' . $resource['appid'],
                6,
                $this->id,
                400
            );
        }

        // Validate the account exists.
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            $this->logger->error('api', 'Unauthorised: this is the core application');
            throw new ApiException(
                'Unauthorised: this is the core application',
                6,
                $this->id,
                400
            );
        }

        // Validate the resource does not already exist.
        $resourceExists = $this->resourceMapper->findByAppIdMethodUri(
            $resource['appid'],
            $resource['method'],
            $resource['uri']
        );
        if (!empty($resourceExists->getresid())) {
            $this->logger->error('api', 'Resource already exists');
            throw new ApiException('Resource already exists', 6, $this->id, 400);
        }
    }
}
