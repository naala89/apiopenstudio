<?php

/**
 * Update an applications.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Db;

class ApplicationUpdate extends Core\ProcessorEntity
{
    /**
     * @var Db\AccountMapper
     */
    protected $accountMapper;

    /**
     * @var Db\ApplicationMapper
     */
    protected $applicationMapper;

    /**
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Application update',
        'machineName' => 'application_update',
        'description' => 'Update an application.',
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
            'appid' => [
                'description' => 'The application iD.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
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
        $this->accountMapper = new Db\AccountMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $appid = $this->val('appid', true);
        $accid = $this->val('accid', true);
        $name = $this->val('name', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application->getAccid())) {
            throw new ApiException("Application ID does not exist: $appid", 6, $this->id, 417);
        }

        if (!$this->userRoleMapper->hasRole($uid, 'Administrator')) {
            if ((
                    !empty($accid)
                    && $this->userRoleMapper->findByUidAppidRolename($uid, $appid, 'Account manager')
                )
                && !$this->userRoleMapper->findByUidAppidRolename($uid, $application->getAccid(), 'Account manager')) {
                throw new ApiException("Permission denied.", 6, $this->id, 417);
            }
        }

        if (!empty($accid)) {
            $account = $this->accountMapper->findByAccid($accid);
            if (empty($account->getAccid())) {
                throw new ApiException("Account ID does not exist: $accid", 6, $this->id, 417);
            }
            $application->setAccid($accid);
        }
        if (!empty($name)) {
            if (preg_match('/[^a-z_\-0-9]/i', $name)) {
                throw new Core\ApiException("Invalid application name: $name. Only underscore, hyphen or alhpanumeric characters permitted.", 6, $this->id, 400);
            }
            $application->setName($name);
        }

        return $this->applicationMapper->save($application);
    }
}
