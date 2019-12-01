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
                'default' => '',
            ],
            'username' => [
                'description' => 'The username of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                // phpcs:ignore
                'description' => 'User keyword to filter by, this is applied to username, first name, last name and email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'orderBy' => [
                'description' => 'Order by column.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['uid', 'username', 'name_first', 'name_last', 'email'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction.',
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

        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
        $orderBy = empty($orderBy) ? 'uid' : $orderBy;
        $direction = $this->val('direction', true);
        $direction = empty($direction) ? 'asc' : $direction;
        $userMapper = new Db\UserMapper($this->db);

        if (!empty($uid)) {
            // Find by UID.
            $users = $userMapper->findByUid($uid);
            if (empty($users->getUid())) {
                throw new Core\ApiException("User does not exist, uid: $uid", 6, $this->id, 400);
            };
        } elseif (!empty($username)) {
            // Find by username.
            $users = $userMapper->findByUsername($username);
            if (empty($users->getUid())) {
                throw new Core\ApiException("User does not exist, username: $username", 6, $this->id, 400);
            }
        } elseif (!empty($email)) {
            // Find by email.
            $users = $userMapper->findByEmail($email);
            if (empty($users->getUid())) {
                throw new Core\ApiException("User does not exist, email: $email", 6, $this->id, 400);
            }
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
            $users = $userMapper->findAll($params);
        } else {
          // Fetch all.
            $params = [
                'order_by' => $orderBy,
                'direction' => $direction,
            ];
            $users = $userMapper->findAll($params);
        }

        $result = [];
        foreach ($users as $user) {
            $u = $user->dump();
            $result[$u['uid']] = $u;
        }

        return $result;
    }
}
