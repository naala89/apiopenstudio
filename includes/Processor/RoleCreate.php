<?php

/**
 * Class RoleCreate.
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
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\RoleMapper;

/**
 * Class RoleCreate
 *
 * Processor class to create a role.
 */
class RoleCreate extends Core\ProcessorEntity
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
        'name' => 'Role create',
        'machineName' => 'role_create',
        'description' => 'Create a role.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The name of the role.',
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
     * RoleCreate constructor.
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

        $name = $this->val('name', true);

        $role = $this->roleMapper->findByName($name);
        if (!empty($role->getRid())) {
            throw new Core\ApiException("A role with the name '$name' already exists", 7, $this->id);
        }

        $role->setName($name);
        if (!$this->roleMapper->save($role)) {
            throw new Core\ApiException("Failed to create role, please check the logs.", 7, $this->id, 400);
        }

        $role = $this->roleMapper->findByName($name);
        return new Core\DataContainer($role->dump(), 'array');
    }
}
