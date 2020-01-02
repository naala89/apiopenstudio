<?php

/**
 * Delete variable in var store.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Db\VarStoreMapper;

class VarStoreDelete extends Core\ProcessorEntity
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
        'name' => 'Var store delete',
        'machineName' => 'var_store_delete',
        'description' => 'Delete a var store variable.',
        'menu' => 'Admin',
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

        return new Core\DataContainer($this->varStoreMapper->delete($varStore));
    }
}
