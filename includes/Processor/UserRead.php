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
        'description' => 'Fetch a single or multiple users.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'The user ID of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'username' => [
                'description' => 'The username of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'User keyword to filter by, this is applied to username, first name, last name and email.',
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
    public function __construct($meta, &$request, $db) {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new Db\UserMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
        $orderBy = empty($orderBy) ? 'uid' : $orderBy;
        $direction = $this->val('direction', true);

        if ($uid > 0) {
            // Find by UID.
            $user = $this->userMapper->findByUid($uid);
            if (empty($user->getUid())) {
                throw new Core\ApiException("User does not exist, uid: $uid", 6, $this->id, 400);
            };
            return $user->dump();
        } elseif (!empty($username)) {
            // Find by username.
            $user = $this->userMapper->findByUsername($username);
            if (empty($user->getUid())) {
                throw new Core\ApiException("User does not exist, username: $username", 6, $this->id, 400);
            }
            return $user->dump();
        } elseif (!empty($email)) {
            // Find by email.
            $user = $this->userMapper->findByEmail($email);
            if (empty($user->getUid())) {
                throw new Core\ApiException("User does not exist, email: $email", 6, $this->id, 400);
            }
            return $user->dump();
        } elseif (!empty($keyword)) {
            // Find by keyword.
            $params = [
            'filter' => [
                ['keyword' => "%$keyword%", 'column' => 'username'],
                ['keyword' => "%$keyword%", 'column' => 'name_first'],
                ['keyword' => "%$keyword%", 'column' => 'name_last'],
                ['keyword' => "%$keyword%", 'column' => 'email'],
            ],
            'order_by' => $orderBy,
            'direction' => $direction,
            ];
            $users = $this->userMapper->findAll($params);
        } else {
          // Fetch all.
            $params = [
                'order_by' => $orderBy,
                'direction' => $direction,
            ];
            $users = $this->userMapper->findAll($params);
        }

        $result = [];
        foreach ($users as $user) {
            $u = $user->dump();
            $result[$u['uid']] = $u;
        }

        return $result;
    }
}
