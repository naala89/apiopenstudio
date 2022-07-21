<?php

/**
 * Class RoleRead.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\RoleMapper;

/**
 * Class RoleRead
 *
 * Processor class to fetch a role.
 */
class RoleRead extends ProcessorEntity
{
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
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['rid', 'name'],
                'default' => null,
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
                'default' => null,
            ],
        ],
    ];

    /**
     * Role mapper class.
     *
     * @var RoleMapper
     */
    private RoleMapper $roleMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->roleMapper = new RoleMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $rid = $this->val('rid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        if (!empty($rid)) {
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
     * @return DataContainer
     *
     * @throws ApiException Error.
     */
    private function findByRid(int $rid): DataContainer
    {
        try {
            $role = $this->roleMapper->findByRid($rid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($role->getRid())) {
            throw new ApiException("Unknown role: $rid", 6, $this->id, 400);
        }
        return new DataContainer($role->dump(), 'array');
    }

    /**
     * Find all roles.
     *
     * @param array $params SQL query params.
     *
     * @return DataContainer An array of associative arrays of a roles rows.
     *
     * @throws ApiException Error.
     */
    private function findAll(array $params): DataContainer
    {
        try {
            $result = $this->roleMapper->findAll($params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $roles = [];
        foreach ($result as $item) {
            $roles[] = $item->dump();
        }
        return new DataContainer($roles, 'array');
    }
}
