<?php

/**
 * Create a role.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\RoleMapper;

class RoleCreate extends Core\ProcessorEntity
{
    /**
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Role create',
        'machineName' => 'role_create',
        'description' => 'Create a role.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The name of the role.',
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
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->roleMapper = new RoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $name = $this->val('name', true);

        $role = $this->roleMapper->findByName($name);
        if (!empty($role->getRid())) {
            throw new Core\ApiException("A role with the name '$name' already exists", 7, $this->id);
        }

        $role->setName($name);

        return $this->roleMapper->save($role);
    }
}
