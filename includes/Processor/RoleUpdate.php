<?php

/**
 * Class RoleUpdate.
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
 * Class RoleUpdate
 *
 * Processor class to update a role.
 */
class RoleUpdate extends Core\ProcessorEntity
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
        'name' => 'Role update',
        'machineName' => 'role_update',
        'description' => 'Update a role.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'The ID of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'name' => [
                'description' => 'The new name of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => '',
            ]
        ],
    ];

    /**
     * RoleUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
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

        return new Core\DataContainer($this->roleMapper->save($role), 'boolean');
    }
}
