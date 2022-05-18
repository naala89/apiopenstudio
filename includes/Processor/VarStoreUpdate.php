<?php

/**
 * Class VarStoreUpdate.
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
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreUpdate
 *
 * Processor class to update a var-store variable.
 */
class VarStoreUpdate extends Core\ProcessorEntity
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
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store update',
        'machineName' => 'var_store_update',
        //phpcs:ignore
        'description' => 'Update a global variable. This is available to all resources within an application group. The specific var_store can be referenced by either the unique vid or appid/key pair.',
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
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'key' => [
                'description' => 'Var store key.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'Var store appid.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'val' => [
                'description' => 'New value for the var.',
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
     * VarStoreUpdate constructor.
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

        $vid = $this->val('vid', true);
        $key = $this->val('key', true);
        $appid = $this->val('appid', true);
        $val = $this->val('val', true);
        $validateAccess = $this->val('validate_access', true);

        if (!empty($vid)) {
            try {
                $var = $this->varStoreMapper->findByVid($vid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($var->getVid())) {
                throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
            }
        } elseif (!empty($key) && !empty($appid)) {
            try {
                $var = $this->varStoreMapper->findByAppIdKey($appid, $key);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($var->getVid())) {
                throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
            }
        } else {
            throw new Core\ApiException(
                'Cannot find VarStore, required vid or appid + key',
                6,
                $this->id,
                400
            );
        }

        if ($validateAccess) {
            // Validate access to the existing var's application
            $permitted = false;
            $currentAppid = $var->getAppid();
            try {
                $roles = Core\Utilities::getRolesFromToken();
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            foreach ($roles as $role) {
                if ($role['appid'] == $currentAppid && in_array($role['role_name'], $this->permittedRoles)) {
                    $permitted = true;
                }
            }
            if (!$permitted) {
                throw new Core\ApiException("permission denied", 6, $this->id, 400);
            }

            // Validate access to the var's NEW application
            if (!empty($appid)) {
                $permitted = false;
                foreach ($roles as $role) {
                    if ($role['appid'] == $appid && in_array($role['role_name'], $this->permittedRoles)) {
                        $permitted = true;
                    }
                }
                if (!$permitted) {
                    throw new Core\ApiException("permission denied", 6, $this->id, 400);
                }
            }
        }

        if (!empty($val)) {
            $var->setVal($val);
        }
        if ($appid > 0) {
            $var->setAppid($appid);
        }
        if (!empty($key)) {
            $var->setKey($key);
        }

        try {
            $this->varStoreMapper->save($var);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer($var->dump(), 'array');
    }
}
