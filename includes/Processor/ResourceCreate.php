<?php

/**
 * Fetch list of resources.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core\Config;
use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\Resource;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Core\ResourceValidator;

class ResourceCreate extends Core\ProcessorEntity
{
    /**
     * @var Config
     */
    private $settings;

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
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * @var ResourceValidator
     */
    private $validator;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource create',
        'machineName' => 'resource_read',
        'description' => 'Create a resource.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The resource name.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'description' => [
                'description' => 'The resource description.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
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
                'limitTypes' => ['string'],
                'limitValues' => ['get', 'post', 'put', 'delete'],
                'default' => '',
            ],
            'uri' => [
                'description' => 'The resource URI.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
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
                'description' => 'Sort resource metadata format type (json or yaml).',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['json', 'yaml'],
                'default' => '',
            ],
            'meta' => [
                'description' => 'Sort resource metadata.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
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
        $this->accountMapper = new AccountMapper($db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->validator = new ResourceValidator();
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $name = $this->val('name', true);
        $description = $this->val('description', true);
        $appid = $this->val('appid', true);
        $method = $this->val('method', true);
        $uri = $this->val('uri', true);
        $format = $this->val('format', true);
        $meta = $this->val('meta', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application)) {
            throw new Core\ApiException("Invalid application: $appid", 6, $this->id, 400);
        }
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
        ) {
            throw new Core\ApiException("Unauthorised: this is the core application", 6, $this->id, 400);
        }
        $userRole = $this->userRoleMapper->findByFilter([
            'appid' => $appid,
            'rid' => 4,
        ]);
        if (empty($userRole)) {
            throw new Core\ApiException('Permission denied', 6, $this->id, 400);
        }
        $resource = $this->resourceMapper->findByAppIdMethodUri($appid, $method, $uri);
        if (!empty($resource->getresid())) {
            throw new Core\ApiException('Resource already exists', 6, $this->id, 400);
        }

        $array = $this->translateMetaString($format, $meta);

        $this->validator->validate($array);

        return $this->create($name, $description, $appid, $method, $uri, $meta);
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
                $array = \Spyc::YAMLLoadString($string);
                if (empty($yaml)) {
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
        return $array;
    }

    /**
     * Create the resource in the DB.
     *
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
    private function create($name, $description, $method, $uri, $appid, $ttl, $meta)
    {
        $resource = new Resource(
            null,
            $appid,
            $name,
            $description,
            strtolower($method),
            strtolower($uri),
            $meta,
            $ttl
        );
        return new Core\DataContainer(
            $this->resourceMapper->save($resource) ? 'true' : 'false',
            'text'
        );
    }
}
