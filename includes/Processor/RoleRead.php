<?php

/**
 * Class RoleRead.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db\RoleMapper;
use Monolog\Logger;

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
    private $roleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
     * RoleRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->roleMapper = new RoleMapper($db);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

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
    private function findByRid(int $rid)
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
     * @return array An array of associative arrays of a roles rows.
     *
     * @throws Core\ApiException Error.
     */
    private function findAll(array $params)
    {
        $result = $this->roleMapper->findAll($params);
        $roles = [];
        foreach ($result as $item) {
            $roles[] = $item->dump();
        }
        return new Core\DataContainer($roles, 'array');
    }
}
