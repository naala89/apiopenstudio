<?php

/**
 * Delete a role.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\RoleMapper;

class RoleDelete extends Core\ProcessorEntity
{
    /**
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Role delete',
        'machineName' => 'role_delete',
        'description' => 'Delete a role.',
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
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $rid = $this->val('rid', true);

        if ($rid < 6) {
            throw new Core\ApiException("Cannot delete core roles.", 7, $this->id);
        }

        $role = $this->roleMapper->findByRid($rid);
        if (empty($role->getRid())) {
            throw new Core\ApiException("A role with RID: $rid does not exist", 7, $this->id);
        }

        return $this->roleMapper->delete($role);
    }
}
