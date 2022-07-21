<?php

/**
 * Class ResourceCreate.
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
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Core\ResourceValidator;

/**
 * Class ResourceCreate
 *
 * Processor class to create a resource.
 */
class ResourceCreate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Resource create',
        'machineName' => 'resource_create',
        'description' => 'Create a resource.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The resource name.',
                'cardinality' => [1, 1],
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
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'method' => [
                'description' => 'The resource HTTP method.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'put', 'delete'],
                'default' => null,
            ],
            'uri' => [
                'description' => 'The resource URI.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'ttl' => [
                'description' => 'The resource TTL in seconds.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'metadata' => [
                'description' => 'The resource metadata (security and process sections) as a JSON string.',
                'cardinality' => [1, 1],
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
     * Config class.
     *
     * @var Config
     */
    private Config $settings;

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
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
        $this->validator = new ResourceValidator($db, $logger);
        $this->settings = new Config();
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

        $name = $this->val('name', true);
        $description = $this->val('description', true);
        $appid = $this->val('appid', true);
        $method = $this->val('method', true);
        $uri = $this->val('uri', true);
        $ttl = $this->val('ttl', true);
        $metadata = $this->val('metadata', true);
        $schema = $this->val('openapi', true);

        try {
            $this->validate(
                $name,
                $description,
                $uri,
                $method,
                $appid,
                $ttl,
                json_decode($metadata, true),
                json_decode($schema, true)
            );
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $resource = new Resource(
            null,
            $appid,
            $name,
            $description,
            strtolower($method),
            strtolower($uri),
            $metadata,
            '',
            $ttl
        );

        try {
            $application = $this->applicationMapper->findByAppid($appid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($application->getOpenapi())) {
            try {
                $account = $this->accountMapper->findByAccid($application->getAccid());
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            $openApiParentClassName = Utilities::getOpenApiParentClassPath($this->settings);
            $openApiParentClass = new $openApiParentClassName();
            $openApiParentClass->setDefault($account->getName(), $application->getName());
            $application->setOpenapi($openApiParentClass->export());
            try {
                $this->applicationMapper->save($application);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
        }

        if (empty($schema)) {
            $openApiPathClassName = Utilities::getOpenApiPathClassPath($this->settings);
            $openApiPathClass = new $openApiPathClassName();

            $openApiPathClass->setDefault($resource);
            $resource->setOpenapi($openApiPathClass->export());
        } else {
            $resource->setOpenapi($schema);
        }

        try {
            $this->resourceMapper->save($resource);
            $resource = $this->resourceMapper->findByAppIdMethodUri($appid, $method, $uri);
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
     * Validate the input.
     *
     * @param string $name Resource name.
     * @param string $description Resource Description.
     * @param string $uri Resource request URI.
     * @param string $method Resource request method.
     * @param int $appid  Resource application ID.
     * @param int $ttl Resource cache TTL.
     * @param array $metadata Resource definition metadata.
     * @param ?array $schema Resource OpenAPI schema.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validate(
        string $name,
        string $description,
        string $uri,
        string $method,
        int $appid,
        int $ttl,
        array $metadata,
        ?array $schema
    ): void {
        // Validate the application exists.
        try {
            $application = $this->applicationMapper->findByAppid($appid);
        } catch (ApiException $e) {
            throw new ApiException("Invalid application: $appid", 6, $this->id, 400);
        }

        // Validate user has developer role for the application
        try {
            $userRoles = Utilities::getRolesFromToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $userHasAccess = false;
        foreach ($userRoles as $userRole) {
            if ($userRole['role_name'] == 'Developer' && $userRole['appid'] == $appid) {
                $userHasAccess = true;
            }
        }
        if (!$userHasAccess) {
            throw new ApiException('Permission denied', 4, $this->id, 403);
        }

        // Validate the resource does not already exist.
        $resource = $this->resourceMapper->findByAppIdMethodUri($appid, $method, $uri);
        if (!empty($resource->getresid())) {
            throw new ApiException('Resource already exists', 6, $this->id, 400);
        }

        // Validate the metadata.
        try {
            $this->validator->validate([
                'name' => $name,
                'description' => $description,
                'uri' => $uri,
                'method' => $method,
                'appid' => $appid,
                'ttl' => $ttl,
                'meta' => $metadata,
                'schema' => $schema,
            ]);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
