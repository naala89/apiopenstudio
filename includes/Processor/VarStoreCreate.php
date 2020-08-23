<?php

/**
 * Create variables in var store.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Db\VarStore;
use Gaterdata\Db\VarStoreMapper;

class VarStoreCreate extends Core\ProcessorEntity
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
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * @var array
     *   Roles that can access vars.
     */
    private $roles = ['Developer', 'Application manager'];

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var store create',
        'machineName' => 'var_store_create',
        'description' => 'Create a variable in the var store.',
        'menu' => 'Var store',
        'input' => [
            'token' => [
                'description' => 'the calling Users token.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
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
            'appid' => [
                'description' => 'Application ID that the var will be assigned to.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'key' => [
                'description' => 'The variable key',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text', 'float'],
                'limitValues' => [],
                'default' => '',
            ],
            'val' => [
                'description' => 'The variable value',
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
        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $val = $this->val('val', true);

        if ($validateAccess) {
            if (!$this->userRoleMapper->findByUidAppidRolename($currentUser->getUid(), $appid, 'Application manager')
                && !$this->userRoleMapper->findByUidAppidRolename($currentUser->getUid(), $appid, 'Developer')) {
                throw new Core\ApiException("permission denied (appid: $appid)", 6, $this->id, 400);
            }
        }

        $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);
        if (!empty($varStore->getVid())) {
            throw new Core\ApiException("var store already exists", 6, $this->id, 400);
        }

        $varStore = new VarStore(null, $appid, $key, $val);

        return new Core\DataContainer($this->varStoreMapper->save($varStore));
    }
}
