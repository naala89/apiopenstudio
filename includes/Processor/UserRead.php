<?php

/**
 * User read.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class UserRead extends Core\ProcessorEntity
{
    /**
     * @var Db\UserMapper
     */
    private $userMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'User read',
        'machineName' => 'user_read',
        'description' => 'Fetch a single or multiple users. Admin',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The current token of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uid' => [
                'description' => 'Filter the results by user ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'username' => [
                'description' => 'Filter the results by username.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'Filter the results by email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'Filter the results by keyword. This is applied to username.',
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
                'limitValues' => ['uid', 'username', 'name_first', 'name_last', 'email'],
                'default' => 'username',
            ],
            'direction' => [
                'description' => 'Order by direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new Db\UserMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $token = $this->val('token', true);
        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
        $orderBy = empty($orderBy) ? 'uid' : $orderBy;
        $direction = $this->val('direction', true);

        $currentUser = $this->userMapper->findBytoken($token);

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

        $users = $this->userMapper->findAllByPermissions($currentUser->getUid(), $params);
        if (empty($users)) {
            throw new Core\ApiException("User not found", 6, $this->id, 400);
        }

        $result = [];
        foreach ($users as $user) {
            $result[] = $user->dump();
        }

        return new Core\DataContainer($result, 'array');
    }

}
