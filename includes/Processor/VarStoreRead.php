<?php

/**
 * Fetch var store variables.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\VarStoreMapper;

class VarStoreRead extends Core\ProcessorEntity
{
    /**
     * @var VarStoreMapper
     */
    private $varStoreMapper;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var array
     *   Roles that can access var store.
     */
    private $roles = ['Developer', 'Application manager'];

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var store read',
        'machineName' => 'var_store_read',
        'description' => 'Fetch a single or multiple var store variables. These will be the variables that belong to the application. If the application is core, then all vars are returned.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'User ID of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'vid' => [
                'description' => 'Var ID. If "all", all vars will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => 'all',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['rid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
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
        $this->varStoreMapper = new VarStoreMapper($db);
        $this->userMapper = new UserMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $vid = $this->val('vid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $user = $this->userMapper->findByUid($uid);
        if (empty($user->getUid())) {
            throw new Core\ApiException("Invalid user ID: $uid", 6, $this->id, 400);
        }

        if ($vid != 'all') {
            // return a var by its vid in the applications where the user has required app/role access.
            return $this->findByUidRolesVid($uid, $vid);
        }

        // return vars in the applications where the user has required app/role access.
        return $this->findByUidRolesAll($uid);
    }

    /**
     * Find a var by vid with user/role validation against the var's application.
     *
     * @param integer $uid
     *   User ID.
     * @param $vid
     *   Var ID.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException
     */
    private function findByUidRolesVid($uid, $vid)
    {
        $var = $this->varStoreMapper->findByUidRolesVid($uid, $this->roles, $vid);

        if (empty($var->getVid())) {
            throw new Core\ApiException("Unknown variable ID or access denied: $vid", 6, $this->id, 400);
        }

        return new Core\DataContainer($var->dump(), 'array');
    }

    /**
     * Find all vars with user/role validation against the vars's application.
     *
     * @param integer $uid
     *   User ID.
     *
     * @return Core\DataContainer
     *   An array of associative arrays of a roles rows.
     *
     * @throws Core\ApiException
     */
    private function findByUidRolesAll($uid)
    {
        $vars = $this->varStoreMapper->findByUidRolesAll($uid, $this->roles);

        if (empty($vars)) {
            throw new Core\ApiException("no variables available or access denied", 6, $this->id, 400);
        }

        $result = [];
        foreach ($vars as $var) {
            $result[$var->getVid()] = $var->dump();
        }

        return new Core\DataContainer($result, 'array');
    }
}
