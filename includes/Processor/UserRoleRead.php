<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * User_role table Read.
 */

class UserRoleRead extends Core\ProcessorEntity
{
    /**
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'User Role read',
        'machineName' => 'user_role_read',
        'description' => 'Fetch a single or all user roles (this is limited by the calling users permissions).',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The requesting users token.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uid' => [
                'description' => 'The user id of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'accid' => [
                'description' => 'The account ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'appid' => [
                'description' => 'The application ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'rid' => [
                'description' => 'The user role ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'order_by' => [
                'description' => 'The column to order the results by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['uid', 'accid', 'appid', 'rid'],
                'default' => 'uid',
            ],
            'direction' => [
                'description' => 'The direction to order the results.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new Db\UserMapper($db);
        $this->userRoleMapper = new Db\UserRoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $rid = $this->val('rid', true);
        $order_by = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if ($uid > 0) {
            $params['col']['uid'] = $uid;
        }
        if ($accid > 0) {
            $params['col']['accid'] = $accid;
        }
        if ($appid > 0) {
            $params['col']['appid'] = $appid;
        }
        if ($rid > 0) {
            $params['col']['rid'] = $rid;
        }
        if (!empty($order_by)) {
            $params['order_by'] = $order_by;
        }
        if (!empty($order_by)) {
            $params['direction'] = $direction;
        }

        $userRoles = $this->userRoleMapper->findForUidWithFilter($currentUser->getUid(), $params);

        $result = [];
        foreach ($userRoles as $userRole) {
            $result[] = $userRole->dump();
        }

        return new Core\DataContainer($result, 'array');
    }
}
