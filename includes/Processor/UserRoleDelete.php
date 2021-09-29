<?php

/**
 * Class UserRoleDelete.
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

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class UserRoleDelete
 *
 * Processor class to delete a user role.
 */
class UserRoleDelete extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User Role delete',
        'machineName' => 'user_role_delete',
        'description' => 'Delete a role for a user.',
        'menu' => 'Admin',
        'input' => [
            'urid' => [
                'description' => 'The user role ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

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

        $urid = $this->val('urid', true);

        $userRoleMapper = new Db\UserRoleMapper($this->db, $this->logger);

        $userRoles = $userRoleMapper->findByFilter(['col' => [
        'urid' => $urid,
        ]]);
        $userRole = $userRoles[0];
        if (empty($userRole->getUrid())) {
            throw new Core\ApiException('User role does not exist', 6, $this->id, 400);
        }
        if ($userRole->getUrid() == 1) {
            $userRoles = $userRoleMapper->findByFilter(['col' => [
            'rid' => 1,
            ]]);
            if (count($userRoles) < 2) {
                throw new Core\ApiException('Cannot delete Administrator role if only one exists', 6, $this->id, 400);
            }
        }

        return new Core\DataContainer($userRoleMapper->delete($userRole), 'bool');
    }
}
