<?php
/**
 * Class VarStoreRead.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use ApiOpenStudio\Db\VarStoreMapper;
use Monolog\Logger;

/**
 * Class VarStoreRead
 *
 * Processor class to fetch a var-store variable.
 */
class VarStoreRead extends Core\ProcessorEntity
{
    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private $varStoreMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private $userMapper;

    /**
     * Array of roles that can access the var store.
     *
     * @var array Roles that can access var store.
     */
    private $roles = ['Developer', 'Application manager'];

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Var store read',
        'machineName' => 'var_store_read',
        // phpcs:ignore
        'description' => 'Fetch a single or multiple var store variables. These will be the variables that belong to the application. If the application is core, then all vars are returned.',
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
                'description' => 'Var ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'Application ID ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['vid', 'key', 'appid', 'val'],
                'default' => 'vid',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    /**
     * VarStoreRead constructor.
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
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if (!empty($vid)) {
            $params['filter'][] = ['keyword' => $vid, 'column' => 'vid`'];
        }
        if (!empty($appid)) {
            $params['filter'][] = ['value' => (integer) $appid, 'column' => 'appid'];
        }
        if (!empty($keyword)) {
            $params['filter'][] = ['keyword' => "%$keyword%", 'column' => '`key`'];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = "`$orderBy`";
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        if ($validateAccess) {
            // return vars in the applications where the user has required app/role access.
            $vars = $this->varStoreMapper->findByUid($currentUser->getUid(), $params);
        } else {
            $vars = $this->varStoreMapper->findAll($params);
        }
        $result = [];
        foreach ($vars as $var) {
            $result[] = $var->dump();
        }

        return $result;
    }
}
