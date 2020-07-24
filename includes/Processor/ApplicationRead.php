<?php

/**
 * Fetch a single or multiple applications.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class ApplicationRead extends Core\ProcessorEntity
{
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
        'name' => 'Application read',
        'machineName' => 'application_read',
        'description' => 'Fetch a single or multiple applications.',
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
            'accountId' => [
                // phpcs:ignore
                'description' => 'Account ID to fetch to filter by. NULL or empty will not filter by account.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'applicationId' => [
                // phpcs:ignore
                'description' => 'Application ID to filter by. NULL or empty will not filter by application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                'description' => 'Application keyword to filter by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'orderBy' => [
                'description' => 'Order by column.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['accid', 'appid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
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
        $accountId = $this->val('accountId', true);
        $applicationId = $this->val('applicationId', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
        $direction = $this->val('direction', true);

        // Filter params.
        $params = [];
        if (!empty($accountId)) {
            $params['filter'][] = [
                'keyword' => $accountId,
                'column' => 'accid',
            ];
        }
        if (!empty($applicationId)) {
            $params['filter'][] = [
                'keyword' => $applicationId,
                'column' => 'appid',
            ];
        }
        if (!empty($keyword)) {
            $params['filter'][] = [
                'keyword' => "%$keyword%",
                'column' => 'name',
            ];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        $applications = $this->applicationMapper->findByUid($uid, $params);
        $result = [];
        foreach ($applications as $application) {
            $result[$application->getAppid()] = [
                'accid' =>$application->getAccid(),
                'appid' => $application->getAppid(),
                'name' => $application->getName(),
            ];
        }

        return new Core\DataContainer($result, 'array');
    }
}
