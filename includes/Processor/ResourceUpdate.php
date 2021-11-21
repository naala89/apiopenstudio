<?php

/**
 * Class ResourceUpdate.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Core\ResourceValidator;
use ReflectionException;
use Spyc;

/**
 * Class ResourceUpdate
 *
 * Processor class to update a resource.
 */
class ResourceUpdate extends Core\ProcessorEntity
{
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
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'description' => [
                'description' => 'The resource description.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application ID the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'The resource HTTP method.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'put', 'delete'],
                'default' => '',
            ],
            'uri' => [
                'description' => 'The resource URI.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
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
                'description' => 'The resource metadata (security and process sections) as a JSON string',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ResourceUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
        $this->accountMapper = new AccountMapper($this->db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
        $this->validator = new ResourceValidator($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
        $resid = $this->val('resid', true);
        $name = $this->val('name', true);
        $description = $this->val('description', true);
        $appid = $this->val('appid', true);
        $method = $this->val('method', true);
        $uri = str_replace("\\/", '/', $this->val('uri', true));
        $ttl = $this->val('ttl', true);
        $metadata = $this->val('metadata', true);

        $resource = $this->resourceMapper->findByResid($resid);
        $application = $this->applicationMapper->findByAppid($resource->getAppId());

        // Invalid resource.
        if (empty($resource->getResid())) {
            throw new Core\ApiException("Resource does not exist: $resid", 6, $this->id, 400);
        }

        // Validate user role access to the resource or the proposed resource.
        $existingResourceRoles = $this->userRoleMapper->findByUidAppidRolename(
            $uid,
            $application->getAppid(),
            'Developer'
        );
        $proposedResourceRoles = $this->userRoleMapper->findByUidAppidRolename(
            $uid,
            $appid,
            'Developer'
        );
        if (empty($existingResourceRoles) || empty($proposedResourceRoles)) {
            throw new Core\ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

        // Update to core application and is locked.
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException("Unauthorised: this is a core resource", 6, $this->id, 400);
        }

        try {
            $this->validator->validate(json_decode($metadata, true));
        } catch (ReflectionException $e) {
            throw new Core\ApiException($e->getMessage(), 6, $this->id, 400);
        }

        if (!$this->update($resid, $name, $description, $method, $uri, $appid, $ttl, $metadata)) {
            throw new Core\ApiException(false, 'boolean');
        }
        $result = $this->resourceMapper->findByResid($resid);

        return new Core\DataContainer($result->dump(), 'array');
    }

    /**
     * Create the resource in the DB.
     *
     * @param integer $resid The resource ID.
     * @param string $name The resource name.
     * @param string $description The resource description.
     * @param string $method The resource method.
     * @param string $uri The resource URI.
     * @param integer $appid The resource application ID.
     * @param integer $ttl The resource application TTL.
     * @param string $meta The resource metadata json encoded string.
     *
     * @return boolean
     *   Create resource result.
     *
     * @throws Core\ApiException DB exception.
     */
    private function update(
        int $resid,
        string $name,
        string $description,
        string $method,
        string $uri,
        int $appid,
        int $ttl,
        string $meta
    ): bool {
        $resource = new Resource(
            $resid,
            $appid,
            $name,
            $description,
            strtolower($method),
            strtolower($uri),
            $meta,
            $ttl
        );

        return $this->resourceMapper->save($resource);
    }
}
