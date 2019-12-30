<?php

/**
 * Fetch list of roles.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\RoleMapper;

class RoleRead extends Core\ProcessorEntity
{
    /**
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Role read',
        'machineName' => 'role_read',
        'description' => 'List a single or all roles.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'Role ID to fetch. If "all", all roles will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => '',
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
        $this->roleMapper = new RoleMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $rid = $this->val('rid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        if ($rid != 'all') {
            return $this->findByRid($rid);
        }

        $params = $this->generateParams($keyword, ['name'], $orderBy, $direction);
        return $this->findAll($params);
    }

    /**
     * Fetch a role by a rid.
     *
     * @param integer $rid
     *   A role ID.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException
     */
    private function findByRid($rid)
    {
        $role = $this->roleMapper->findByRid($rid);
        if (empty($role->getRid())) {
            throw new Core\ApiException("Unknown role: $rid", 6, $this->id, 400);
        }
        return new Core\DataContainer($role->dump(), 'array');
    }

    /**
     * Find all roles.
     *
     * @param array
     *   SQL query params.
     *
     * @return array
     *   An array of associative arrays of a roles rows.
     *
     * @throws Core\ApiException
     */
    private function findAll($params)
    {
        $result = $this->roleMapper->findAll($params);
        $roles = [];
        foreach ($result as $item) {
            $roles[] = $item->dump();
        }
        return new Core\DataContainer($roles, 'array');
    }
}
