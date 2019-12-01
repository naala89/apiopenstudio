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
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Account read',
        'machineName' => 'account_read',
        'description' => 'Fetch a single or all accounts.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The account ID or "all".',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string', 'integer'],
                'limitValues' => [],
                'default' => 'all',
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'Keyword to filter by in the account name. This is only used iwhen getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'Order by column. This is only used when getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['accid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction. This is only used when getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['asc', 'desc'],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $accid = $this->val('accid', true);

        $accountMapper = new Db\AccountMapper($this->db);

        if ($accid == 'all') {
          // Only need to add filters if fetching all.
            $keyword = $this->val('keyword', true);
            $orderBy = $this->val('order_by', true);
            $direction = $this->val('direction', true);
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

            $rows = $accountMapper->findAll($params);
            $result = [];
            foreach ($rows as $row) {
                $result[$row->getAccid()] = $row->getName();
            }
            return $result;
        }

        $account = $accountMapper->findByAccid(intval($accid));
        if (empty($account->getAccid())) {
            throw new Core\ApiException('Account does not exist: ' . intval($accid), 6, $this->id, 400);
        }
        return $account->dump();
    }
}
