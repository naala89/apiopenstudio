<?php

/**
 * Class VarStoreCreate.
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
        'description' => 'Create a variable in the var store. The return result is an error object (on failure) or the newly created object.',
        'menu' => 'Var store',
        'input' => [
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
                'default' => '',
            ],
            'val' => [
                'description' => 'The variable value',
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
     * VarStoreCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
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

        $permitted = false;
        $roles = Core\Utilities::getRolesFromToken();
        $accounts = [];
        foreach ($roles as $role) {
            if ($role['role_name'] == 'Administrator' && in_array('Administrator', $this->permittedRoles)) {
                $permitted = true;
            } elseif ($role['role_name'] == 'Account manager' && in_array('Account manager', $this->permittedRoles)) {
                $accid = $role['accid'];
                if (!isset($accounts[$accid])) {
                    $accountsObjects = $this->applicationMapper->findByAccid($accid);
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
        if (!$permitted) {
            throw new Core\ApiException("permission denied", 6, $this->id, 400);
        }

        $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);
        if (!empty($varStore->getVid())) {
            throw new Core\ApiException("var store already exists", 6, $this->id, 400);
        }

        $varStore = new VarStore(null, $appid, $key, $val);

        $this->varStoreMapper->save($varStore);
        $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);

        return new Core\DataContainer($varStore->dump(), 'array');
    }
}
