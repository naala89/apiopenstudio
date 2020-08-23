<?php

/**
 * Update a role.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\RoleMapper;

class RoleUpdate extends Core\ProcessorEntity
{
    /**
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Role update',
        'machineName' => 'role_update',
        'description' => 'Update a role.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'The ID of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'name' => [
                'description' => 'The new name of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => '',
            ]
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->roleMapper = new RoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $rid = $this->val('rid', true);
        $name = $this->val('name', true);

        if ($rid < 6) {
            throw new Core\ApiException("Cannot update core roles.", 7, $this->id);
        }

        $role = $this->roleMapper->findByName($name);
        if (!empty($role->getRid())) {
            throw new Core\ApiException("A role with the name '$name' already exists", 7, $this->id);
        }
        $role = $this->roleMapper->findByRid($rid);
        if (empty($role->getRid())) {
            throw new Core\ApiException("A role with RID: $rid does not exist", 7, $this->id);
        }

        $role->setName($name);

        return $this->roleMapper->save($role);
    }
}
