<?php

/**
 * Class ResourceImport.
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
use ReflectionException;
use Symfony\Component\Yaml\Exception\ParseException;
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Core\ResourceValidator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ResourceImport
 *
 * Processor class to import a resource
 */
class ResourceImport extends Core\ProcessorEntity
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
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
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
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
        $roles = Core\Utilities::getRolesFromToken();
        $resource = $this->val('resource');

        // Only developer role permitted to upload resource.
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['role_name'] == 'Developer') {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new Core\ApiException(
                'unauthorized for this call',
                4,
                $this->id,
                401
            );
        }

        // Validate the YAML/JSON.
        $resource = $resource->getType() == 'file' ? file_get_contents($resource->getData()) : $resource->getData();
        if ($value = json_decode($resource, true)) {
            $resource = $value;
        } else {
            try {
                $value = Yaml::parse($resource);
                $resource = $value;
            } catch (ParseException $exception) {
                $message = 'Unable to parse the YAML string: ' . $exception->getMessage();
                throw new Core\ApiException(
                    $message,
                    6,
                    $this->id,
                    400
                );
            }
        }

        $this->logger->debug('api', 'Decoded new resource: ' . print_r($resource, true));

        // Validate required keys in the imported file.
        foreach ($this->requiredKeys as $requiredKey) {
            if (!isset($resource[$requiredKey])) {
                $this->logger->error('api', "Missing $requiredKey in new resource");
                throw new Core\ApiException("Missing $requiredKey in new resource", 6, $this->id, 400);
            }
        }
        // Validate TTL in the imported file.
        if ($resource['ttl'] < 0) {
            $this->logger->error('api', 'Negative ttl in new resource');
            throw new Core\ApiException("Negative ttl in new resource", 6, $this->id, 400);
        }

        // Validate user has developer role for the appid.
        $role = $this->userRoleMapper->findByUidAppidRolename(
            $uid,
            $resource['appid'],
            'Developer'
        );
        if (empty($role->getUrid())) {
            $this->logger->error('api', 'Unauthorised: you do not have permissions for this application');
            throw new Core\ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

        // Validate the application exists.
        $application = $this->applicationMapper->findByAppid($resource['appid']);
        if (empty($application)) {
            $this->logger->error('api', 'Invalid application: ' . $resource['appid']);
            throw new Core\ApiException(
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
            throw new Core\ApiException(
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
            throw new Core\ApiException('Resource already exists', 6, $this->id, 400);
        }

        // Merge the sections into final metadata.
        $meta = [];
        if (isset($resource['security'])) {
            $meta = array_merge($meta, ['security' => $resource['security']]);
        }
        if (isset($resource['process'])) {
            $meta = array_merge($meta, ['process' => $resource['process']]);
        }
        if (isset($resource['output'])) {
            $meta = array_merge($meta, ['output' => $resource['output']]);
        }

        // Validate the metadata.
        try {
            $this->validator->validate($meta);
        } catch (Core\ApiException | ReflectionException $e) {
            throw new Core\ApiException($e->getMessage(), 6, $this->id, 400);
        }

        if (
            !$this->create(
                $resource['name'],
                $resource['description'],
                $resource['method'],
                $resource['uri'],
                $resource['appid'],
                $resource['ttl'],
                json_encode($meta)
            )
        ) {
            throw new Core\ApiException(false, 'boolean');
        }
        $result = $this->resourceMapper->findByAppIdMethodUri(
            $resource['appid'],
            $resource['method'],
            $resource['uri']
        );

        return new Core\DataContainer($result->dump(), 'array');
    }

    /**
     * Create the resource in the DB.
     *
     * @param string $name The resource name.
     * @param string $description The resource description.
     * @param string $method The resource method.
     * @param string $uri The resource URI.
     * @param integer $appid The resource application ID.
     * @param integer $ttl The resource application TTL.
     * @param string $meta The resource metadata json encoded string.
     *
     * @return Core\DataContainer Create resource result.
     *
     * @throws Core\ApiException
     */
    private function create(
        string $name,
        string $description,
        string $method,
        string $uri,
        int $appid,
        int $ttl,
        string $meta
    ): Core\DataContainer {
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
        if (!$this->resourceMapper->save($resource)) {
            return new Core\DataContainer(false);
        }
        $resource = $this->resourceMapper->findByAppIdMethodUri($appid, strtolower($method), strtolower($uri));
        return new Core\DataContainer($resource->dump(), 'array');
    }
}
