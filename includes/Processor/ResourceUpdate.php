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
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Core\ResourceValidator;
use Spyc;
use Monolog\Logger;

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
            'format' => [
                'description' => 'The resource metadata format type (json or yaml).',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['json', 'yaml'],
                'default' => 'yaml',
            ],
            'meta' => [
                'description' => 'The resource metadata (security and process sections) as a YAML or JSON string',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json', 'text'],
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
     * @param Logger $logger Logger object.
     *
     * @throws Core\ApiException
     */
    public function __construct($meta, &$request, ADOConnection $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($this->db);
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
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

        // Resource is locked.
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException("Unauthorised: this is a core resource", 6, $this->id, 400);
        }

        // Proposed account/application are locked.
        $application = $this->applicationMapper->findByAppid($appid);
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException(
                "Unauthorised: this is a core resource",
                6,
                $this->id,
                400
            );
        }

        $meta = $this->translateMetaString($format, $meta);
        $this->validator->validate(json_decode($meta, true));

        return $this->update($resid, $name, $description, $method, $uri, $appid, $ttl, $meta);
    }

    /**
     * Covert a string in a format into a JSON encoded string.
     *
     * @param string $format The format of the input string.
     * @param string $string The metadata string.
     *
     * @return string|false Normalised string format.
     *
     * @throws Core\ApiException Invalid data.
     */
    private function translateMetaString(string $format, string $string): ?string
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
     * @param integer $resid The resource ID.
     * @param string $name The resource name.
     * @param string $description The resource description.
     * @param string $method The resource method.
     * @param string $uri The resource URI.
     * @param integer $appid The resource application ID.
     * @param integer $ttl The resource application TTL.
     * @param string $meta The resource metadata json encoded string.
     *
     * @return Core\DataContainer
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
    ): Core\DataContainer {
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
