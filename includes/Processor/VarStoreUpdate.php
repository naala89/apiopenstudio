<?php

/**
 * Class VarStoreUpdate.
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
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreUpdate
 *
 * Processor class to update a var-store variable.
 */
class VarStoreUpdate extends Core\ProcessorEntity
{
    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private VarStoreMapper $varStoreMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private UserRoleMapper $userRoleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store update',
        'machineName' => 'var_store_update',
        'description' => 'Update a var store variable.',
        'menu' => 'Var store',
        'input' => [
            'validate_access' => [
                // phpcs:ignore
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => 'all',
            ],
            'val' => [
                'description' => 'New value for the var.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
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

        $uid = Core\Utilities::getUidFromToken();
        $validateAccess = $this->val('validate_access', true);
        $vid = $this->val('vid', true);
        $val = $this->val('val', true);

        $var = $this->varStoreMapper->findByVid($vid);
        if (empty($var->getVid())) {
            throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
        }

        if ($validateAccess) {
            if (
                !$this->userRoleMapper->findByUidAppidRolename(
                    $uid,
                    $var->getAppid(),
                    'Application manager'
                )
                    && !$this->userRoleMapper->findByUidAppidRolename(
                        $uid,
                        $var->getAppid(),
                        'Developer'
                    )
            ) {
                throw new Core\ApiException('permission denied (appid: ' . $var->getAppid() . ')', 6, $this->id, 400);
            }
        }

        $var->setVal($val);

        return new Core\DataContainer($this->varStoreMapper->save($var), 'text');
    }
}
