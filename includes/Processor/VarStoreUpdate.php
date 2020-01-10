<?php

/**
 * Create variable in var store.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Db\VarStoreMapper;

class VarStoreUpdate extends Core\ProcessorEntity
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
     * @var array
     *   Roles that can access var store.
     */
    private $roles = ['Developer', 'Application manager'];

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var store update',
        'machineName' => 'var_store_update',
        'description' => 'Update a var store variable.',
        'menu' => 'Var store',
        'input' => [
            'uid' => [
                'description' => 'User ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => 'all',
            ],
            'val' => [
                'description' => 'New value for the var.',
                'cardinality' => [1, 1],
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
        $this->userRoleMapper = new UserRoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $vid = $this->val('vid', true);
        $val = $this->val('val', true);

        $varStore = $this->varStoreMapper->findByVId($vid);
        if (empty($varStore->getVid())) {
            throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
        }

        $appid = $varStore->getAppid();

        $permitted = false;
        foreach ($this->roles as $role) {
            $result = $this->userRoleMapper->findByUidAppidRolename($uid, $appid, $role);
            $permitted = !empty($result->getUrid()) ? true : $permitted;
        }
        if (!$permitted) {
            throw new Core\ApiException("permission denied for appid: $appid", 6, $this->id, 400);
        }

        $varStore->setVal($val);

        return new Core\DataContainer($this->varStoreMapper->save($varStore));
    }
}