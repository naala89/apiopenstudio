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
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Db\VarStore;
use ApiOpenStudio\Db\VarStoreMapper;
use Monolog\Logger;

/**
 * Class VarStoreCreate
 *
 * Processor class to create a var-store variable.
 */
class VarStoreCreate extends Core\ProcessorEntity
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
        'name' => 'Var store create',
        'machineName' => 'var_store_create',
        'description' => 'Create a variable in the var store.',
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
     * @param Logger $logger Logger object.
     *
     * @throws Core\ApiException
     */
    public function __construct($meta, &$request, ADOConnection $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
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
        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $val = $this->val('val', true);

        if ($validateAccess) {
            if (
                !$this->userRoleMapper->findByUidAppidRolename($uid, $appid, 'Application manager')
                && !$this->userRoleMapper->findByUidAppidRolename($uid, $appid, 'Developer')
            ) {
                throw new Core\ApiException("permission denied (appid: $appid)", 6, $this->id, 400);
            }
        }

        $varStore = $this->varStoreMapper->findByAppIdKey($appid, $key);
        if (!empty($varStore->getVid())) {
            throw new Core\ApiException("var store already exists", 6, $this->id, 400);
        }

        $varStore = new VarStore(null, $appid, $key, $val);

        return new Core\DataContainer($this->varStoreMapper->save($varStore));
    }
}
