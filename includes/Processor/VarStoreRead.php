<?php

/**
 * Fetch var store variables.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Db\VarStoreMapper;

class VarStoreRead extends Core\ProcessorEntity
{
    /**
     * @var VarStoreMapper
     */
    private $varStoreMapper;

    /**
     * @var UserRoleMapper
     */
    private $userRoleMapper;

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
        'menu' => 'Var store',
        'input' => [
            'token' => [
                'description' => 'The token of the user making the call. This is used to validate the user permissions.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'validate_access' => [
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'vid' => [
                'description' => 'Var ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'Application ID ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
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
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['vid', 'key', 'appid', 'val'],
                'default' => 'vid',
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
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $validateAccess = $this->val('validate_access', true);
        $vid = $this->val('vid', true);
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if (!empty($vid)) {
            $params['filter'][] = ['keyword' => $vid, 'column' => 'vid`'];
        }
        if (!empty($appid)) {
            $params['filter'][] = ['value' => (integer) $appid, 'column' => 'appid'];
        }
        if (!empty($keyword)) {
            $params['filter'][] = ['keyword' => "%$keyword%", 'column' => '`key`'];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = "`$orderBy`";
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        if ($validateAccess) {
            // return vars in the applications where the user has required app/role access.
            $vars = $this->varStoreMapper->findByUid($currentUser->getUid(), $params);
        }
        else {
            $vars = $this->varStoreMapper->findAll($params);
        }
        $result = [];
        foreach ($vars as $var) {
            $result[] = $var->dump();
        }

        return $result;
    }
}
