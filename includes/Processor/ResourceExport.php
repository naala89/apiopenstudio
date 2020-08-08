<?php

/**
 * Download a resource file.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\Resource;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRole;
use Gaterdata\Db\UserRoleMapper;
use Symfony\Component\Yaml\Yaml;

class ResourceExport extends Core\ProcessorEntity
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
     * @var ApplicationMapper
     */
    private $applicationMapper;

    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource export',
        'machineName' => 'resource_export',
        'description' => 'Export a resource file.',
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
                'description' => 'The Resource ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'format' => [
                'description' => 'The format to save the file as.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['yaml', 'json'],
                'default' => 'yaml',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->settings = new Core\Config();
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
        $format = $this->val('format', true);

        $resource = $this->resourceMapper->findByResid($resid);
        if (empty($resource->getResid())) {
            throw new Core\ApiException('Invalid resource', 6, $this->id, 400);
        }

        $role = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $resource->getAppid(),
            'Developer');
        if (empty($role->getUrid())) {
            throw new Core\ApiException("Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400);
        }

        switch ($format) {
            case 'yaml':
                header('Content-Disposition: attachment; filename="resource.twig"');
                return $this->getYaml($resource);
                break;
            case 'json':
                header('Content-Disposition: attachment; filename="resource.json"');
                return $this->getJson($resource);
                break;
        }
    }

    /**
     * Create a YAML string from a resource.
     *
     * @param Resource $resource
     *   The resource.
     * @return string
     *   A YAML string.
     */
    private function getYaml(Resource $resource) {
        $obj = [];
        $obj['name'] = $resource->getName();
        $obj['description'] = $resource->getDescription();
        $obj['uri'] = $resource->getUri();
        $obj['method'] = $resource->getMethod();
        $obj['appid'] = $resource->getAppId();
        $obj['ttl'] = $resource->getTtl();
        $obj = array_merge($obj, json_decode($resource->getMeta(), true));
        return  Yaml::dump($obj, Yaml::PARSE_OBJECT);
    }

    /**
     * Create a JSON string from a resource.
     *
     * @param Resource $resource
     *   The resource.
     * @return string
     *   A YAML string.
     */
    private function getJson(Resource $resource) {
        $obj = [];
        $obj['name'] = $resource->getName();
        $obj['description'] = $resource->getDescription();
        $obj['uri'] = $resource->getUri();
        $obj['method'] = $resource->getMethod();
        $obj['appid'] = $resource->getAppId();
        $obj['ttl'] = $resource->getTtl();
        $obj = array_merge($obj, json_decode($resource->getMeta(), true));
        return json_encode($obj);
    }
}
