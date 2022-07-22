<?php

/**
 * Class ValidateTokenRoles.
 *
 * @package    ApiOpenStudio\Security
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Security;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Db\Application;

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
    protected array $details = [
        'name' => 'Validate token roles',
        'machineName' => 'validate_token_roles',
        'description' => 'Validate that the user has a valid token and role.',
        'menu' => 'Security',
        'input' => [
            'roles' => [
                'description' => 'User roles that are permitted.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['collection'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
            ],
            'validate_account' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource account. If false, the result will be if the user has the role in any accounts.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'validate_application' => [
                // phpcs:ignore
                'description' => 'Validate The has has the role in the resource application. If false, the result will be if the user has the role in any applications.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
        ],
    ];

    /**
     * @var Application current request Application object.
     */
    protected Application $application;

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

        $permittedRoles = $this->val('roles', true);
        $validateAccount = $this->val('validate_account', true);
        $validateApplication = $this->val('validate_application', true);

        // Get roles and validate the user against them.
        if (!$this->validatePermitted($permittedRoles, $validateAccount, $validateApplication)) {
            throw new ApiException('permission denied', 4, $this->id, 403);
        }

        return new DataContainer(true, 'boolean');
    }

    /**
     * Validate a user against roles and the account/application of the resource.
     *
     * @param array $permittedRoles Permitted roles for the call.
     * @param bool $validateAccount Validate account.
     * @param bool $validateApplication Validate application.
     *
     * @return boolean
     */
    protected function validatePermitted(array $permittedRoles, bool $validateAccount, bool $validateApplication): bool
    {
        foreach ($this->roles as $role) {
            if (in_array($role['role_name'], $permittedRoles)) {
                switch ($role['role_name']) {
                    case 'Administrator':
                        return true;
                    case 'Account manager':
                        if (!$validateAccount || $this->request->getAccId() == $role['accid']) {
                            return true;
                        }
                        break;
                    default:
                        if (!$validateAccount && !$validateApplication) {
                            return true;
                        } elseif (!$validateAccount && $validateApplication) {
                            if ($this->request->getAppId() == $role['appid']) {
                                return true;
                            }
                        } elseif ($validateAccount && !$validateApplication) {
                            if ($this->request->getAccId() == $role['accid']) {
                                return true;
                            }
                        } elseif (
                            $this->request->getAccId() == $role['accid'] &&
                            $this->request->getAppId() == $role['appid']
                        ) {
                            return true;
                        }
                        break;
                }
            }
        }

        return false;
    }
}
