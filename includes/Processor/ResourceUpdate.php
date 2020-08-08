<?php

/**
 * Update a resource.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core\Config;
use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\Resource;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Core\ResourceValidator;
use Spyc;

class ResourceUpdate extends Core\ProcessorEntity
{
    /**
     * @var Config
     */
    private $settings;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * @var ApplicationMapper
     */
    private $applicationMapper;

    /**
     * @var ResourceValidator
     */
    private $validator;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource update',
        'machineName' => 'resource_update',
        'description' => 'Update a resource.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The token of the user making the call. This is used to validate the user permissions.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'resid' => [
                'description' => 'The resource ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'name' => [
                'description' => 'The resource name.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'description' => [
                'description' => 'The resource description.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application ID the resource is associated with.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'The resource HTTP method.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'put', 'delete'],
                'default' => '',
            ],
            'uri' => [
                'description' => 'The resource URI.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'ttl' => [
                'description' => 'The resource TTL in seconds.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'format' => [
                'description' => 'The resource metadata format type (json or yaml).',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['json', 'yaml'],
                'default' => 'yaml',
            ],
            'meta' => [
                'description' => 'The resource metadata (security and process sections) as a YAML or JSON string',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['json', 'text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->settings = new Config();
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($this->db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->validator = new ResourceValidator($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $resid = $this->val('resid', true);
        $name = $this->val('name', true);
        $description = $this->val('description', true);
        $appid = $this->val('appid', true);
        $method = $this->val('method', true);
        $uri = $this->val('uri', true);
        $ttl = $this->val('ttl', true);
        $format = $this->val('format', true);
        $meta = $this->val('meta', true);

        $resource = $this->resourceMapper->findByResid($resid);
        $application = $this->applicationMapper->findByAppid($resource->getAppId());

        // Invalid resource.
        if (empty($resource->getResid())) {
            throw new Core\ApiException("Resource does not exist: $resid", 6, $this->id, 400);
        }

        // Validate user role access to the resource or the proposed resource.
        $existingResourceRoles = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $application->getAppid(),
            'Developer');
        $proposedResourceRoles = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $appid,
            'Developer');
        if (empty($existingResourceRoles) || empty($proposedResourceRoles)) {
            throw new Core\ApiException("Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400);
        }

        // Resource is locked.
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException("Unauthorised: this is a core resource", 6, $this->id, 400);
        }

        // Proposed application/method/uri combination already exist.
        $test = $this->resourceMapper->findByAppIdMethodUri($appid, $method, $uri);
        if (!empty($test->getResid()) && $test->getResid() != $resid) {
            throw new Core\ApiException(
                'A resource with this method and uri already exists for the application',
                6,
                $this->id,
                400);
        }

        // Proposed account/application are locked.
        $application = $this->applicationMapper->findByAppid($appid);
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException("Unauthorised: this is a core resource",
                6,
                $this->id,
                400);
        }

        $meta = $this->translateMetaString($format, $meta);
        $this->validator->validate(json_decode($meta, true));

        return $this->update($resid, $name, $description, $method, $uri, $appid, $ttl, $meta);
    }

    /**
     * Covert a string in a format into an associative array.
     *
     * @param $format
     *   The format of the input string.
     * @param $string
     *   The metadata string.
     *
     * @return array|mixed
     *   Normalised string format.
     *
     * @throws Core\ApiException
     */
    private function translateMetaString($format, $string)
    {
        $array = [];
        switch ($format) {
            case 'yaml':
                $array = Spyc::YAMLLoadString($string);
                if (empty($array)) {
                    throw new Core\ApiException('Invalid or no YAML supplied', 6, $this->id, 417);
                }
                break;
            case 'json':
                $array = json_decode(json_encode($string), true);
                if (empty($array)) {
                    throw new Core\ApiException('Invalid or no JSON supplied', 6, $this->id, 417);
                }
                break;
            default:
                break;
        }
        return json_encode($array);
    }

    /**
     * Create the resource in the DB.
     *
     * @param integer $resid
     *   The resource ID.
     * @param string $name
     *   The resource name.
     * @param string $description
     *   The resource description.
     * @param string $method
     *   The resource method.
     * @param string $uri
     *   The resource URI.
     * @param integer $appid
     *   The resource application ID.
     * @param integer $ttl
     *   The resource application TTL.
     * @param string $meta
     *   The resource metadata json encoded string.
     *
     * @return Core\DataContainer
     *   Create resource result.
     *
     * @throws Core\ApiException
     */
    private function update($resid, $name, $description, $method, $uri, $appid, $ttl, $meta)
    {
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

        return new Core\DataContainer($this->resourceMapper->save($resource) ? 'true' : 'false', 'text');
    }
}
