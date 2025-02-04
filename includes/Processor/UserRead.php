<?php

/**
 * Class UserRead.
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
use ApiOpenStudio\Db\UserMapper;

/**
 * Class UserRead
 *
 * Processor class to fetch a user.
 */
class UserRead extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User read',
        'machineName' => 'user_read',
        'description' => 'Fetch a single or multiple users. Admin',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'Filter the results by user ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'username' => [
                'description' => 'Filter the results by username.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'email' => [
                'description' => 'Filter the results by email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'Filter the results by keyword. This is applied to username.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'orderBy' => [
                'description' => 'Order by column.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['uid', 'username', 'name_first', 'name_last', 'email', 'active'],
                'default' => 'username',
            ],
            'direction' => [
                'description' => 'Order by direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db, $logger);
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

        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
        $orderBy = empty($orderBy) ? 'uid' : $orderBy;
        $direction = $this->val('direction', true);

        try {
            $currentUser = $this->userMapper->findByUid(Utilities::getClaimFromToken('uid'));
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $params = [];
        if ($uid > 0) {
            $params['filter'][] = ['keyword' => $uid, 'column' => 'uid'];
        }
        if (!empty($username)) {
            $params['filter'][] = ['keyword' => $username, 'column' => 'username'];
        }
        if (!empty($email)) {
            $params['filter'][] = ['keyword' => $email, 'column' => 'email'];
        }
        if (!empty($keyword)) {
            $params['filter'][] = ['keyword' => "%$keyword%", 'column' => 'username'];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        try {
            $users = $this->userMapper->findAllByPermissions($currentUser->getUid(), $params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($users)) {
            throw new ApiException("User not found", 6, $this->id, 400);
        }

        $result = [];
        foreach ($users as $user) {
            $result[] = $user->dump();
        }

        return new DataContainer($result, 'array');
    }
}
