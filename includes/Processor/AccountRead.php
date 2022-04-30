<?php

/**
 * Class AccountRead.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;

/**
 * Class AccountRead
 *
 * Processor class to fetch an account.
 */
class AccountRead extends ProcessorEntity
{
    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    private AccountMapper $accountMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Account read',
        'machineName' => 'account_read',
        'description' => 'Fetch a single or all accounts.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'Filter by accid. If empty then all accounts the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'Keyword to filter by in the account name. This is only used iwhen getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'Order by column. This is only used when getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['accid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction. This is only used when getting "all" accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => '',
            ],
        ],
    ];

    /**
     * AccountRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        try {
            $uid = Utilities::getUidFromToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $accid = $this->val('accid', true);
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
        if (!empty($accid)) {
            $params['filter'][] = [
                'keyword' => $accid,
                'column' => 'accid',
            ];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        try {
            $accounts = $this->accountMapper->findAllForUser($uid, $params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $result = [];
        foreach ($accounts as $account) {
            $result[] = $account->dump();
        }

        try {
            $result = new DataContainer($result, 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        return $result;
    }
}
