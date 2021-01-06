<?php

/**
 * Class ResourceDelete.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use Monolog\Logger;

/**
 * Class ResourceDelete
 *
 * Processor class to delete a resource.
 */
class ResourceDelete extends Core\ProcessorEntity
{
    /**
     * Config class.
     *
     * @var Config
     */
    private $settings;

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
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Resource delete',
        'machineName' => 'resource_delete',
        'description' => 'Delete a resource.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                // phpcs:ignore
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
                'default' => '',
            ],
        ],
    ];

    /**
     * ResourceDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->settings = new Config();
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

        $resid = $this->val('resid', true);
        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);

        $resource = $this->resourceMapper->findByResid($resid);
        if (empty($resource->getResid())) {
            throw new Core\ApiException("Invalid resource: $resid", 6, $this->id, 400);
        }

        $role = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $resource->getAppid(),
            'Developer'
        );
        if (empty($role->getUrid())) {
            throw new Core\ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

        $application = $this->applicationMapper->findByAppid($resource->getAppid());
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (
            $account->getName() == $this->settings->__get(['api', 'core_account'])
            && $application->getName() == $this->settings->__get(['api', 'core_application'])
            && $this->settings->__get(['api', 'core_resource_lock'])
        ) {
            throw new Core\ApiException("Unauthorised: this is a core resource", 6, $this->id, 400);
        }

        return new Core\DataContainer($this->resourceMapper->delete($resource) ? 'true' : 'false');
    }
}
