<?php

/**
 * Class ApplicationCreate.
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
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class ApplicationCreate
 *
 * Processor class to create an application.
 */
class ApplicationCreate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Application create',
        'machineName' => 'application_create',
        'description' => 'Create an application.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'openapi' => [
                'description' => 'The OpenApi schema fragment for the application.',
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
     * Config class.
     *
     * @var Config
     */
    protected Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new UserRoleMapper($this->db, $logger);
        $this->userMapper = new UserMapper($this->db, $logger);
        $this->accountMapper = new AccountMapper($this->db, $logger);
        $this->applicationMapper = new ApplicationMapper($this->db, $logger);
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

        $accid = $this->val('accid', true);
        $name = $this->val('name', true);
        $openApi = $this->val('openapi', true);

        try {
            $uid = Utilities::getClaimFromToken('uid');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
        ) {
            throw new ApiException('permission denied', 4, $this->id, 403);
        }

        if (preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new ApiException(
                "Invalid application name: $name. Only underscore, hyphen or alphanumeric characters permitted.",
                6,
                $this->id,
                400
            );
        }


        try {
            $account = $this->accountMapper->findByAccid($accid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($account->getAccid())) {
            throw new ApiException('Account does not exist: "' . $accid . '"', 6, $this->id, 400);
        }

        try {
            $application = $this->applicationMapper->findByAccidAppname($accid, $name);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($application->getAppid())) {
            throw new ApiException(
                "Application already exists ($name in account: " . $account->getName() . ")",
                6,
                $this->id,
                400
            );
        }

        try {
            $openApiParentClassName = Utilities::getOpenApiParentClassPath($this->settings);
            $openApiParentClass = new $openApiParentClassName();
            if (!empty($openApi)) {
                $openApiParentClass->import($openApi);
            } else {
                $openApiParentClass->setDefault($account->getName(), $name);
            }

            $application = new Application(null, $accid, $name, $openApiParentClass->export());
            $this->applicationMapper->save($application);

            $application = $this->applicationMapper->findByAccidAppname($accid, $name);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        try {
            $result = $application->dump();
            $result['openapi'] = json_decode($result['openapi']);
            $result = new DataContainer($result, 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}
