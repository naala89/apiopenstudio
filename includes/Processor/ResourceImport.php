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

use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Core\ResourceValidator;
use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;

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
    private $requiredKeys = [
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
    private $settings;

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private $userMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    private $applicationMapper;

    /**
     * Resource validator class.
     *
     * @var ResourceValidator
     */
    private $validator;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Resource import',
        'machineName' => 'resource_import',
        'description' => 'Import a resource from a file.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                // phpcs:ignore
                'description' => 'The token of the user making the call. This is used to validate the user permissions.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
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
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->validator = new ResourceValidator($db, $this->logger);
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

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $resource = $this->val('resource');

        $resource = $resource->getType() == 'file' ? file_get_contents($resource->getData()) : $resource->getData();
        if ($value = json_decode($resource, true)) {
            $resource = $value;
        } else {
            try {
                $value = Yaml::parse($resource);
                $resource = $value;
            } catch (ParseException $exception) {
                throw new Core\ApiException(
                    'Unable to parse the YAML string: ',
                    $exception->getMessage(),
                    6,
                    $this->id,
                    400
                );
            }
        }

        $this->logger->debug('Decoded new resource: ' . print_r($resource, true));

        foreach ($this->requiredKeys as $requiredKey) {
            if (!isset($resource[$requiredKey])) {
                $this->logger->error("Missing $requiredKey in new resource");
                throw new Core\ApiException("Missing $requiredKey in new resource", 6, $this->id, 400);
            }
        }
        if ($resource['ttl'] < 0) {
            $this->logger->error("Negative ttl in new resource");
            throw new Core\ApiException("Negative ttl in new resource", 6, $this->id, 400);
        }

        $role = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $resource['appid'],
            'Developer'
        );
        if (empty($role->getUrid())) {
            $this->logger->error("Unauthorised: you do not have permissions for this application");
            throw new Core\ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

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

        $application = $this->applicationMapper->findByAppid($resource['appid']);
        if (empty($application)) {
            $this->logger->error('Invalid application: ' . $resource['appid']);
            throw new Core\ApiException(
                'Invalid application: ' . $resource['appid'],
                6,
                $this->id,
                400
            );
        }

        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
                && $application->getName() == $this->settings->__get(['api', 'core_application'])
                && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            $this->logger->error('Unauthorised: this is the core application');
            throw new Core\ApiException(
                'Unauthorised: this is the core application',
                6,
                $this->id,
                400
            );
        }

        $resourceExists = $this->resourceMapper->findByAppIdMethodUri(
            $resource['appid'],
            $resource['method'],
            $resource['uri']
        );
        if (!empty($resourceExists->getresid())) {
            $this->logger->error('Resource already exists');
            throw new Core\ApiException('Resource already exists', 6, $this->id, 400);
        }

        $this->validator->validate($meta);

        return $this->create(
            $resource['name'],
            $resource['description'],
            $resource['method'],
            $resource['uri'],
            $resource['appid'],
            $resource['ttl'],
            json_encode($meta)
        );
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
     */
    private function create(
        string $name,
        string $description,
        string $method,
        string $uri,
        int $appid,
        int $ttl,
        string $meta
    ) {
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
        return new Core\DataContainer($this->resourceMapper->save($resource) ? 'true' : 'false', 'text');
    }
}
