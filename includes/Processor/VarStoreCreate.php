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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Role;
use ApiOpenStudio\Db\VarStore;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreCreate
 *
 * Processor class to create a var-store variable.
 */
class VarStoreCreate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store create',
        'machineName' => 'var_store_create',
        // phpcs:ignore
        'description' => 'Create a global variable. This is available to all resources within an account or application group. The return result is an error object (on failure) or the newly created object.',
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
            'accid' => [
                'description' => 'Account ID that the var will be assigned to. One of accid or appid must be populated',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                // phpcs:ignore
                'description' => 'Application ID that the var will be assigned to. One of accid or appid must be populated',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
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
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
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

        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $val = $this->val('val', true);
        $validateAccess = $this->val('validate_access', true);

        if ((empty($accid) && empty($appid)) || (!empty($accid) && !empty($appid))) {
            throw new ApiException('variable must be assigned to an account or application', 6, $this->id, 400);
        }

        if ($validateAccess) {
            $this->validateCreatePermission($accid, $appid);
        }

        $this->validateExists($accid, $appid, $key);

        $varStore = new VarStore(null, $accid, $appid, $key, $val);
        try {
            $this->varStoreMapper->save($varStore);
            if (!empty($accid)) {
                $varStore = $this->varStoreMapper->findByAccidKey($accid, $key);
            } else {
                $varStore = $this->varStoreMapper->findByAppidKey($appid, $key);
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer($varStore->dump(), 'array');
    }

    /**
     * Validate permissions to create a var store.
     *
     * @param int|null $accid
     * @param int|null $appid
     *
     * @throws ApiException
     */
    protected function validateCreatePermission(?int $accid, ?int $appid)
    {
        try {
            /** @var Role[] */
            $roles = Utilities::getClaimFromToken('roles');
            /** @var Application[] */
            $applications = $this->applicationMapper->findAll();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $permitted = false;
        foreach ($roles as $role) {
            if ($permitted) {
                continue;
            }
            if ($role['role_name'] == 'Administrator') {
                $permitted = true;
            } elseif ($role['role_name'] == 'Account manager') {
                foreach ($applications as $application) {
                    if (!empty($accid)) {
                        if ($application->getAccid() == $accid && $application->getAccid() == $role['accid']) {
                            $permitted = true;
                        }
                    } else {
                        if ($application->getAppid() == $appid && $application->getAccid() == $role['accid']) {
                            $permitted = true;
                        }
                    }
                }
            } elseif ($role['role_name'] == 'Application manager' || $role['role_name'] == 'Developer') {
                foreach ($applications as $application) {
                    if (
                        !empty($appid) &&
                        $application->getAppid() == $appid &&
                        $application->getAppid() == $role['appid']
                    ) {
                        $permitted = true;
                    }
                }
            }
        }
        if (!$permitted) {
            throw new ApiException("permission denied", 4, $this->id, 403);
        }
    }

    /**
     * Validate that a var_store exists.
     *
     * @param int|null $accid
     * @param int|null $appid
     * @param $key
     *
     * @throws ApiException
     */
    protected function validateExists(?int $accid, ?int $appid, $key)
    {
        $exists = false;
        if (!empty($accid)) {
            $varStore = $this->varStoreMapper->findByAccidKey($accid, $key);
            if (!empty($varStore->getVid())) {
                $exists = true;
            }
            $applications = $this->applicationMapper->findByAccid($accid);
            if (empty($applications)) {
                throw new ApiException(
                    'could not find an application that maps to accid',
                    6,
                    $this->id,
                    400
                );
            }
            $varStore = $this->varStoreMapper->findByAppidKey($applications[0]->getAppid(), $key);
            if (!empty($varStore->getVid())) {
                $exists = true;
            }
        }
        if (!empty($appid)) {
            $application = $this->applicationMapper->findByAppid($appid);
            $varStore = $this->varStoreMapper->findByAccidKey($application->getAccid(), $key);
            if (!empty($varStore->getVid())) {
                $exists = true;
            }
            $varStore = $this->varStoreMapper->findByAppidKey($appid, $key);
            if (!empty($varStore->getVid())) {
                $exists = true;
            }
        }
        if ($exists) {
            throw new ApiException(
                'a var_store already exists with this key for the account or application',
                6,
                $this->id,
                400
            );
        }
    }
}
