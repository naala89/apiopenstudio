<?php

/**
 * Account Read.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class AccountRead extends Core\ProcessorEntity
{
    /**
     * @var Db\AccountMapper
     */
    private $accountMapper;
    /**
     * @var Db\UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Account read',
        'machineName' => 'account_read',
        'description' => 'Fetch a single or all accounts.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'User ID of the user making the call. This is used to limit the accounts viewable whilst and still have access by all admin roles.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'accid' => [
                'description' => 'Filter by accid. If empty then all accounts the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'Keyword to filter by in the account name. This is only used iwhen getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'Order by column. This is only used when getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['accid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction. This is only used when getting "all" accounts.',
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
        $this->accountMapper = new Db\AccountMapper($db);
        $this->userRoleMapper = new Db\UserRoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $accids = $this->getUserAccids($uid);

        if ($accid < 1) {
            // Only need to add filters if fetching all.
            $params = [];
            if (!empty($keyword)) {
                $params['filter'][] = [
                    'keyword' => "%$keyword%",
                    'column' => "name",
                ];
            }
            if (!empty($orderBy)) {
                $params['order_by'] = $orderBy;
            }
            if (!empty($direction)) {
                $params['direction'] = $direction;
            }

            $accounts = $this->accountMapper->findAll($params);
            $result = [];
            foreach ($accounts as $account) {
                if (in_array($account->getAccid(), $userAccids)) {
                    $result[$account->getAccid()] = $account->getName();
                }
            }
            return new Core\DataContainer($result, 'array');
        }

        if (!in_array($accid, $userAccids)) {
            throw new Core\ApiException('Account does not exist or you do not have access to this account: ' . $accid, 6, $this->id, 400);
        }

        $account = $this->accountMapper->findByAccid($accid);
        if (empty($account->getAccid())) {
            throw new Core\ApiException('Account does not exist: ' . intval($accid), 6, $this->id, 400);
        }
        return new Core\DataContainer($account->dump(), 'array');
    }
}
