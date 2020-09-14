<?php
/**
 * Class VarStoreUpdate.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;
use Gaterdata\Db\VarStoreMapper;
use Monolog\Logger;

/**
 * Class VarStoreUpdate
 *
 * Processor class to update a var-store variable.
 */
class VarStoreUpdate extends Core\ProcessorEntity
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var VarStoreMapper
     */
    private $varStoreMapper;

    /**
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * @var array
     *   Roles that can access var store.
     */
    private $roles = ['Developer', 'Application manager'];

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var store update',
        'machineName' => 'var_store_update',
        'description' => 'Update a var store variable.',
        'menu' => 'Var store',
        'input' => [
            'token' => [
                // phpcs:ignore
                'description' => 'The token of the user making the call. This is used to validate the user permissions.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'validate_access' => [
                // phpcs:ignore
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => 'all',
            ],
            'val' => [
                'description' => 'New value for the var.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * VarStoreUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $validateAccess = $this->val('validate_access', true);
        $vid = $this->val('vid', true);
        $val = $this->val('val', true);

        $var = $this->varStoreMapper->findByVId($vid);
        if (empty($var->getVid())) {
            throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
        }

        if ($validateAccess) {
            if (!$this->userRoleMapper->findByUidAppidRolename(
                $currentUser->getUid(),
                $var->getAppid(),
                'Application manager')
                    && !$this->userRoleMapper->findByUidAppidRolename(
                        $currentUser->getUid(),
                        $var->getAppid(),
                        'Developer')) {
                throw new Core\ApiException('permission denied (appid: ' . $var->getAppid() . ')', 6, $this->id, 400);
            }
        }

        $var->setVal($val);

        return new Core\DataContainer($this->varStoreMapper->save($var), 'text');
    }
}
