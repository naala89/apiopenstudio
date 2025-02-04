<?php

/**
 * Class ResourceUpdate.
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
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ReflectionException;

/**
 * Class ResourceUpdate
 *
 * Processor class to update a resource.
 */
class ResourceUpdate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Resource update',
        'machineName' => 'resource_update',
        'description' => 'Update a resource.',
        'menu' => 'Admin',
        'input' => [
            'resid' => [
                'description' => 'The resource ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'name' => [
                'description' => 'The resource name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'description' => [
                'description' => 'The resource description.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'The application ID the resource is associated with.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'method' => [
                'description' => 'The resource HTTP method.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'put', 'delete'],
                'default' => null,
            ],
            'uri' => [
                'description' => 'The resource URI.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'ttl' => [
                'description' => 'The resource TTL in seconds.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'metadata' => [
                'description' => 'The resource metadata (security and process sections) as a JSON string',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json'],
                'limitValues' => [],
                'default' => null,
            ],
            'openapi' => [
                'description' => 'The resource OpenApi definition partial, for this path only.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

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
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
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

        $resid = $this->val('resid', true);
        $name = $this->val('name', true);
        $description = $this->val('description', true);
        $appid = $this->val('appid', true);
        $method = $this->val('method', true);
        $uri = str_replace("\\/", '/', $this->val('uri', true));
        $ttl = $this->val('ttl', true);
        $metadata = $this->val('metadata', true);
        $openapi = $this->val('openapi', true);

        try {
            $resource = $this->resourceMapper->findByResid($resid);
            $application = $this->applicationMapper->findByAppid($resource->getAppId());
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        // Invalid resource.
        if (empty($resource->getResid())) {
            throw new ApiException("Resource does not exist: $resid", 6, $this->id, 400);
        }

        $this->validateUserRoleAccess($application, (!empty($appid) ? $appid : null));

        // Generate default OpenApi fragment in the resource is it doesn't already exist.
        if (empty($resource->getOpenapi())) {
            $settings = new Config();
            $openApiPathClassName = Utilities::getOpenApiPathClassPath($settings);
            $openApiPathClass = new $openApiPathClassName();
            $openApiPathClass->setDefault($resource);
            $resource->setOpenapi($openApiPathClass->export());
        }

        if (!empty($uri)) {
            if (isset($schema[$resource->getUri()])) {
                $schema[$uri] = $schema[$resource->getUri()];
                unset($schema[$resource->getUri()]);
            } else {
                $schema[$uri][$resource->getMethod()]['description'] = $description ?? $resource->getDescription();
                $schema[$uri][$resource->getMethod()]['summary'] = $name ?? $resource->getName();
            }
            $resource->setOpenapi(json_encode($schema, JSON_UNESCAPED_SLASHES));
            $resource->setUri($uri);
        }
        if (!empty($method)) {
            if (isset($schema[$resource->getUri()][$resource->getMethod()])) {
                $schema[$resource->getUri()][$method] = $schema[$resource->getUri()][$resource->getMethod()];
                unset($schema[$resource->getUri()][$resource->getMethod()]);
            } else {
                $schema[$uri][$resource->getMethod()]['description'] = $description ?? $resource->getDescription();
                $schema[$uri][$resource->getMethod()]['summary'] = $name ?? $resource->getName();
            }
            $resource->setOpenapi(json_encode($schema, JSON_UNESCAPED_SLASHES));
            $resource->setMethod($method);
        }
        if (!empty($name)) {
            $resource->setName($name);
            $schema[$resource->getUri()][$resource->getMethod()]['summary'] = $name;
            $resource->setOpenapi(json_encode($schema));
        }
        if (!empty($description)) {
            $resource->setDescription($description);
            $schema[$resource->getUri()][$resource->getMethod()]['description'] = $description;
        }
        if (!empty($appid)) {
            $resource->setAppId($appid);
        }
        if (!empty($ttl)) {
            $resource->setTtl($ttl);
        }
        if (!empty($metadata) && $metadata != '"false"') {
            try {
                $this->validator->validate([
                    'name' => $name,
                    'description' => $description,
                    'uri' => $uri,
                    'method' => $method,
                    'appid' => $appid,
                    'ttl' => $ttl,
                    'meta' => json_decode($metadata, true),
                    'schema' => $schema,
                ]);
            } catch (ReflectionException $e) {
                throw new ApiException($e->getMessage(), 6, $this->id, 400);
            }
            $resource->setMeta($metadata);
        }
        if (!empty($openapi) && $openapi != '"false"') {
            $schema = json_decode($openapi, true);
            if (!isset($schema[$resource->getUri()])) {
                throw new ApiException(
                    'invalid OpenApi schema, path ' . $resource->getUri() . ' Does not exist',
                    6,
                    $this->id,
                    400
                );
            }
            if (!isset($schema[$resource->getUri()][$resource->getMethod()])) {
                $uri = $resource->getUri();
                $method = $resource->getMethod();
                throw new ApiException(
                    "invalid OpenApi schema, $uri/$method Does not exist",
                    6,
                    $this->id,
                    400
                );
            }
            if (sizeof($schema) != 1) {
                throw new ApiException(
                    'invalid OpenApi schema, there should only be 1 OpenApi fragment assigned to a resource,',
                    6,
                    $this->id,
                    400
                );
            }
            if (sizeof($schema[$resource->getUri()]) != 1) {
                throw new ApiException(
                    'invalid OpenApi schema, there should be only 1 method inside a path assigned to a resource,',
                    6,
                    $this->id,
                    400
                );
            }
            $schema[$resource->getUri()][$resource->getMethod()]['summary'] = $name;
            $schema[$resource->getUri()][$resource->getMethod()]['description'] = $description;
        }
        $resource->setOpenapi(json_encode($schema, JSON_UNESCAPED_SLASHES));

        try {
            $this->resourceMapper->save($resource);
            $resource = $this->resourceMapper->findByResid($resid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $result = $resource->dump();
        $result['meta'] = json_decode($result['meta'], true);
        $result['openapi'] = json_decode($result['openapi'], true);

        try {
            $result = new DataContainer($result, 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * Validate the user has permissions on the existing or proposed application/resource.
     *
     * @param Application $application
     * @param int|null $newAppid
     *
     * @throws ApiException
     */
    protected function validateUserRoleAccess(Application $application, int $newAppid = null)
    {
        try {
            $uid = Utilities::getClaimFromToken('uid');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (
            empty(
                $this->userRoleMapper->findByUidAppidRolename(
                    $uid,
                    $application->getAppid(),
                    'Developer'
                )
            )
        ) {
            throw new ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

        if (!empty($newAppid)) {
            if (
                empty(
                    $this->userRoleMapper->findByUidAppidRolename(
                        $uid,
                        $newAppid,
                        'Developer'
                    )
                )
            ) {
                throw new ApiException(
                    "Unauthorised: you do not have permissions for this application",
                    6,
                    $this->id,
                    400
                );
            }
        }

        $this->validateCoreProtection($application);
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
