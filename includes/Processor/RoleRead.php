<?php

/**
 * Class RoleRead.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\RoleMapper;

/**
 * Class RoleRead
 *
 * Processor class to fetch a role.
 */
class RoleRead extends Core\ProcessorEntity
{
    /**
     * Role mapper class.
     *
     * @var RoleMapper
     */
    private RoleMapper $roleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Role read',
        'machineName' => 'role_read',
        'description' => 'List a single or all roles.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'Role ID to fetch. If "all", all roles will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['rid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * RoleRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->roleMapper = new RoleMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

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
     * @param integer $rid A role ID.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException Error.
     */
    private function findByRid(int $rid): Core\DataContainer
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
     * @param array $params SQL query params.
     *
     * @return Core\DataContainer An array of associative arrays of a roles rows.
     *
     * @throws Core\ApiException Error.
     */
    private function findAll(array $params): Core\DataContainer
    {
        $result = $this->roleMapper->findAll($params);
        $roles = [];
        foreach ($result as $item) {
            $roles[] = $item->dump();
        }
        return new Core\DataContainer($roles, 'array');
    }
}
