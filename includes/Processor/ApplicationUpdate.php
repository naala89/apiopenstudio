<?php

/**
 * Class ApplicationUpdate.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class ApplicationUpdate
 *
 * Processor class to update an application.
 */
class ApplicationUpdate extends ProcessorEntity
{
    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    protected AccountMapper $accountMapper;

    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    protected UserRoleMapper $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    protected UserMapper $userMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Application update',
        'machineName' => 'application_update',
        'description' => 'Update an application.',
        'menu' => 'Admin',
        'input' => [
            'appid' => [
                'description' => 'The application iD.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'openapi' => [
                'description' => 'The OpenApi schema fragment for the application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ApplicationUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($this->db, $logger);
        $this->applicationMapper = new ApplicationMapper($this->db, $logger);
        $this->userRoleMapper = new UserRoleMapper($this->db, $logger);
        $this->userMapper = new UserMapper($this->db, $logger);
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

        $appid = $this->val('appid', true);
        $accid = $this->val('accid', true);
        $name = $this->val('name', true);
        $schema = $this->val('openapi', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application->getAccid())) {
            throw new ApiException("application ID does not exist: $appid", 6, $this->id, 400);
        }

        $this->validateAccess($application, $accid);
        $openApi = $this->getOpenApi($schema, $application);

        if (!empty($accid)) {
            $account = $this->accountMapper->findByAccid($accid);
            if (empty($account->getAccid())) {
                throw new ApiException("account ID does not exist: $accid", 6, $this->id, 400);
            }
            $application->setAccid($accid);
            $openApi->setAccount($account->getName());
        }

        if (!empty($name)) {
            if (preg_match('/[^a-z_\-0-9]/i', $name)) {
                throw new ApiException(
                    "invalid application name: $name. Only underscore, hyphen or alhpanumeric characters permitted",
                    6,
                    $this->id,
                    400
                );
            }
            $application->setName($name);
            $openApi->setApplication($name);
        }

        $application->setOpenapi($openApi->export());

        if (!$this->applicationMapper->save($application)) {
            throw new ApiException(
                'application update failed, please check the logs',
                6,
                $this->id,
                500
            );
        }
        $result = $application->dump();
        $result['openapi'] = json_decode($result['openapi']);
        return new DataContainer($result, 'array');
    }

    /**
     * Validate user has access rights to update the application.
     *
     * @param Application $application
     * @param int|null $accid
     *
     * @throws ApiException
     */
    protected function validateAccess(Application $application, int $accid = null)
    {
        $uid = Utilities::getUidFromToken();
        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator') &&
            !$this->userRoleMapper->hasAccidRole($uid, $application->getAccid(), 'Account manager') &&
            !empty($accid) &&
            !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
        ) {
            throw new ApiException("permission denied", 4, $this->id, 403);
        }
    }

    /**
     * Return an OpenApi object, based on openapi_version in settings.
     *
     * @param string $inputSchema
     * @param Application $application
     *
     * @return mixed
     *
     * @throws ApiException
     *
     * @todo Validate final schema fragment version against openapi_version in settings.
     */
    protected function getOpenApi(string $inputSchema, Application $application)
    {
        $settings = new Config();
        $openApiParentClassName = Utilities::getOpenApiParentClassPath($settings);
        $openApiParentClass = new $openApiParentClassName();

        if (!empty($inputSchema)) {
            $openApiParentClass->import($inputSchema);
        } elseif (empty($application->getOpenapi())) {
            $account = $this->accountMapper->findByAccid($application->getAccid());
            $openApiParentClass->setDefault($account->getName(), $application->getName());
        } else {
            $openApiParentClass->import($application->getOpenapi());
        }

        return $openApiParentClass;
    }
}
