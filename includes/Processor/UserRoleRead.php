<?php
/**
 * Class UserRoleRead.
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
use ApiOpenStudio\Db;
use Monolog\Logger;

/**
 * Class UserRoleRead
 *
 * Processor class to fetch a user role.
 */
class UserRoleRead extends Core\ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * User role mapper class.
     *
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'User Role read',
        'machineName' => 'user_role_read',
        'description' => 'Fetch a single or all user roles (this is limited by the calling users permissions).',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The requesting users token.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uid' => [
                'description' => 'The user id of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'accid' => [
                'description' => 'The account ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'appid' => [
                'description' => 'The application ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'rid' => [
                'description' => 'The user role ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'order_by' => [
                'description' => 'The column to order the results by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['uid', 'accid', 'appid', 'rid'],
                'default' => 'uid',
            ],
            'direction' => [
                'description' => 'The direction to order the results.',
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
     * UserRoleRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
        $this->userRoleMapper = new Db\UserRoleMapper($db);
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
        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $rid = $this->val('rid', true);
        $order_by = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if ($uid > 0) {
            $params['col']['uid'] = $uid;
        }
        if ($accid > 0) {
            $params['col']['accid'] = $accid;
        }
        if ($appid > 0) {
            $params['col']['appid'] = $appid;
        }
        if ($rid > 0) {
            $params['col']['rid'] = $rid;
        }
        if (!empty($order_by)) {
            $params['order_by'] = $order_by;
        }
        if (!empty($order_by)) {
            $params['direction'] = $direction;
        }

        $userRoles = $this->userRoleMapper->findForUidWithFilter($currentUser->getUid(), $params);

        $result = [];
        foreach ($userRoles as $userRole) {
            $result[] = $userRole->dump();
        }

        return new Core\DataContainer($result, 'array');
    }
}
