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
            'accountIds' => [
                // phpcs:ignore
                'description' => 'An array of the IDs of the account to fetch applications by. NULL or empty will fetch for all accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => '',
            ],
            'applicationNames' => [
                // phpcs:ignore
                'description' => 'An array of the application names. NULL or empty will fetch all applications for the accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => '',
            ],
            'accountFilter' => [
                'description' => 'Account ID to filter by.',
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

        $accountIds = $this->val('accountIds', true);
        $accountIds = empty($accountIds) ? [] : $accountIds;
        $applicationNames = $this->val('applicationNames', true);
        $applicationNames = empty($applicationNames) ? [] : $applicationNames;
        $uid = $this->val('uid', true);

        // Filter params.
        $params = [];
        $accountFilter = $this->val('accountFilter', true);
        if (!empty($accountFilter)) {
            $params['filter'] = [
                'column' => 'accid',
                'keyword' => $accountFilter,
            ];
        }
        $keyword = $this->val('keyword', true);
        if (!empty($keyword)) {
            $params['keyword'] = "%$keyword%";
        }
        $orderBy = $this->val('orderBy', true);
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        $direction = $this->val('direction', true);
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        $applications = $this->applicationMapper->findByUid($uid, $params);
        $result = [];
        foreach ($applications as $application) {
            $result[$application->getAppid()] = [
                'name' => $application->getName(),
                'accid' =>$application->getAccid(),
            ];
        }

        return $result;
    }
}
