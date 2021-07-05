<?php

/**
 * Class ValidateTokenRoles.
 *
 * @package    ApiOpenStudio
 * @subpackage Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core;

/**
 * Class ValidateTokenRoles
 *
 * Provide token authentication based and the user's role.
 *
 * Validation:
 *   If user is Administrator then only against role.
 *   If user is Account manager then against role and account.
 *   All other users against role and application.
 */
class ValidateTokenRoles extends ValidateToken
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Validate token roles',
        'machineName' => 'validate_token_role',
        'description' => 'Validate that the user has a valid token and role.',
        'menu' => 'Security',
        'input' => [
            'roles' => [
                'description' => 'User roles that are permitted.',
                'cardinality' => [1, '*'],
                'literalAllowed' => false,
                'limitProcessors' => ['collection'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
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

        // Get roles and validate the user against them.
        $permittedRoles = $this->val('roles', true);
        if ($this->validateUserRoles($permittedRoles)) {
            return new Core\DataContainer(true, 'boolean');
        }

        throw new Core\ApiException('unauthorized for this call', 4, -1, 401);
    }

    /**
     * Validate a user against roles and the account/application of the resource.
     *
     * @param array $permittedRoles Permitted roles for the call.
     *
     * @return boolean
     */
    protected function validateUserRoles(array $permittedRoles): bool
    {
        foreach ($this->roles as $userRole) {
            // Do not validate accid or appid for Administrator role.
            if (
                $userRole['role_name'] == 'Administrator'
                && in_array('Administrator', $permittedRoles)
            ) {
                return true;
            }
            // Only validate accid for Account manager role.
            if (
                $userRole['role_name'] == 'Account manager'
                && $this->request->getAccId() == $userRole['accid']
            ) {
                return true;
            }
            // Only validate appid for Application manager role.
            if (
                $userRole['role_name'] == 'Application manager'
                && $this->request->getAppId() == $userRole['appid']
            ) {
                return true;
            }
            // Normal user, validate role, accid, appid
            if (
                in_array($userRole['role_name'], $permittedRoles)
                && $this->request->getAccId() == $userRole['accid']
                && $this->request->getAppId() == $userRole['appid']
            ) {
                return true;
            }
        }

        return false;
    }
}
