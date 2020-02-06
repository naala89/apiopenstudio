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
     * @var Db\UserRoleMapper
     */
    protected $applicationMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Application delete',
        'machineName' => 'application_delete',
        'description' => 'Delete an application.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'User ID of the user making the call. This is used to limit the delete applications to account manager with account access and administrators.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
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
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $appid = $this->val('applicationId', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application->getAppid())) {
            throw new ApiException("Delete application, no such appid: $appid",
                6, $this->id, 417);
        }
        $accid = $application->getAccid();

        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
        ) {
            throw new ApiException('Permission denied.', 6, $this->id, 417);
        }

        $resourceMapper = new Db\ResourceMapper($this->db);

        $resources = $resourceMapper->findByAppId($appid);
        if (!empty($resources)) {
            throw new ApiException("Delete application, resources are assigned to this application: $appid",
                6, $this->id, 417);
        }
        $userRoles = $userRoleMapper->findByFilter(['col' => ['appid' => $appid]]);
        if (!empty($userRoles)) {
            throw new ApiException("Delete application, users are assigned to this application: $appid",
                6, $this->id, 417);
        }

        return $applicationMapper->delete($application);
    }
}
