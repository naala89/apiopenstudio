<?php

/**
 * Class VarStoreCreate.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\VarStore;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreCreate
 *
 * Processor class to create a var-store variable.
 */
class VarStoreCreate extends Core\ProcessorEntity
{
    /**
     * @var array|string[] Array of permitted roles
     */
    protected array $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
        'Developer',
    ];

    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private VarStoreMapper $varStoreMapper;

    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    private ApplicationMapper $applicationMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store create',
        'machineName' => 'var_store_create',
        // phpcs:ignore
        'description' => 'Create a global variable. This is available to all resources within an application group. The return result is an error object (on failure) or the newly created object.',
        'menu' => 'Variables',
        'input' => [
            'validate_access' => [
                // phpcs:ignore
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open. By default this is true for security reasons, but to allow consumers to use this in a resource, you will need to set it to false (otherwise access will be denied).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'appid' => [
                'description' => 'Application ID that the var will be assigned to.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
            'key' => [
                'description' => 'The variable key',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text', 'float'],
                'limitValues' => [],
                'default' => null,
            ],
            'val' => [
                'description' => 'The variable value',
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
     * VarStoreCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
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

        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $val = $this->val('val', true);
        $permitted = !($this->val('validate_access', true));

        if (!$permitted) {
            try {
                $roles = Core\Utilities::getRolesFromToken();
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            $accounts = [];
            foreach ($roles as $role) {
                if ($role['role_name'] == 'Administrator' && in_array('Administrator', $this->permittedRoles)) {
                    $permitted = true;
                } elseif (
                    $role['role_name'] == 'Account manager' &&
                    in_array('Account manager', $this->permittedRoles)
                ) {
                    $accid = $role['accid'];
                    if (!isset($accounts[$accid])) {
                        try {
                            $accountsObjects = $this->applicationMapper->findByAccid($accid);
                        } catch (ApiException $e) {
                            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
                        }
                        foreach ($accountsObjects as $accountObject) {
                            $accounts[$accid][] = $accountObject->getAppid();
                        }
                    }
                    if (in_array($appid, $accounts[$accid])) {
                        $permitted = true;
                    }
                } elseif ($role['appid'] == $appid && in_array($role['role_name'], $this->permittedRoles)) {
                    $permitted = true;
                }
            }
        }

        if (!$permitted) {
            throw new Core\ApiException("permission denied", 6, $this->id, 400);
        }

        try {
            $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($varStore->getVid())) {
            throw new Core\ApiException("var store already exists", 6, $this->id, 400);
        }

        $varStore = new VarStore(null, $appid, $key, $val);

        try {
            $this->varStoreMapper->save($varStore);
            $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer($varStore->dump(), 'array');
    }
}
