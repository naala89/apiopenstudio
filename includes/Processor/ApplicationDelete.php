<?php

/**
 * Delete an application.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Db;
use Gaterdata\Db\UserRoleMapper;

class ApplicationDelete extends Core\ProcessorEntity
{
    /**
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * @var Db\UserRoleMapper
     */
    protected $applicationMapper;

    /**
     * @var Db\ResourceMapper
     */
    protected $resourceMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Application delete',
        'machineName' => 'application_delete',
        'description' => 'Delete an application.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'Request token of the user making the call.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 0,
            ],
            'applicationId' => [
                'description' => 'The appication ID of the application.',
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
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
        $this->resourceMapper = new Db\ResourceMapper($this->db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $user = $this->userMapper->findBytoken($token);
        $appid = $this->val('applicationId', true);
        $application = $this->applicationMapper->findByAppid($appid);
        $accid = $application->getAccid();
        if (!$this->userRoleMapper->hasRole($user->getUid(), 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($user->getUid(), $accid, 'Account manager')) {
            throw new ApiException("Permission denied.", 6, $this->id, 417);
        }
        if (empty($application->getAppid())) {
            throw new ApiException("Delete application, invalid appid: $appid",
                6, $this->id, 417);
        }

        $resources = $this->resourceMapper->findByAppId($appid);
        if (!empty($resources)) {
            throw new ApiException("Cannot delete application, resources are assigned to this application: $appid",
                6, $this->id, 417);
        }
        $userRoles = $this->userRoleMapper->findByFilter(['col' => ['appid' => $appid]]);
        if (!empty($userRoles)) {
            throw new ApiException("Cannot delete application, users are assigned to this application: $appid",
                6, $this->id, 417);
        }

        return $this->applicationMapper->delete($application);
    }
}
