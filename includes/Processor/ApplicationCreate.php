<?php

/**
 * Create an applications.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Db;

class ApplicationCreate extends Core\ProcessorEntity
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
     * @var Db\AccountMapper
     */
    protected $accountMapper;

    /**
     * @var Db\ApplicationMapper
     */
    protected $applicationMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Application create',
        'machineName' => 'application_create',
        'description' => 'Create an application.',
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
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [1, 1],
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
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
        $this->accountMapper = new Db\AccountMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $user = $this->userMapper->findBytoken($token);
        $accid = $this->val('accid', true);
        if (!$this->userRoleMapper->hasRole($user->getUid(), 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($user->getUid(), $accid, 'Account manager')
        ) {
            throw new ApiException('Permission denied.', 6, $this->id, 417);
        }

        $name = $this->val('name', true);
        if (preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new Core\ApiException("Invalid application name: $name. Only underscore, hyphen or alhpanumeric characters permitted.", 6, $this->id, 400);
        }

        $account = $this->accountMapper->findByAccid($accid);
        if (empty($account->getAccid())) {
            throw new ApiException('Account does not exist: "' . $accid . '"', 6, $this->id, 400);
        }
        $application = $this->applicationMapper->findByAccidAppname($accid, $name);
        if (!empty($application->getAppid())) {
            throw new ApiException("Application already exists ($name in account: " . $account->getName() . ")", 6, $this->id, 400);
        }


        $application = new Db\Application(null, $accid, $name);
        return $this->applicationMapper->save($application);
    }
}
