<?php

/**
 * Delete a resource.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core\Config;
use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;

class ResourceDelete extends Core\ProcessorEntity
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
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource delete',
        'machineName' => 'resource_delete',
        'description' => 'Delete a resource.',
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
        $this->applicationMapper = new ApplicationMapper($db);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->settings = new Config();
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

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
            'Developer');
        if (empty($role->getUrid())) {
            throw new Core\ApiException("Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400);
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
