<?php

/**
 * Class UserRoleRead.
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
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class UserRoleRead
 *
 * Processor class to fetch a user role.
 */
class UserRoleRead extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User Role read',
        'machineName' => 'user_role_read',
        'description' => 'Fetch a single or all user roles (this is limited by the calling users permissions).',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'The user id of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'accid' => [
                'description' => 'The account ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'The application ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'rid' => [
                'description' => 'The user role ID of user roles.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'order_by' => [
                'description' => 'The column to order the results by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['uid', 'accid', 'appid', 'rid'],
                'default' => 'uid',
            ],
            'direction' => [
                'description' => 'The direction to order the results.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    protected UserMapper $userMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    protected UserRoleMapper $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
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

        try {
            $currentUser = $this->userMapper->findByUid(Utilities::getUidFromToken());
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $uid = $this->val('uid', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $rid = $this->val('rid', true);
        $order_by = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if (!is_null($uid)) {
            $params['col']['uid'] = $uid;
        }
        if (!is_null($accid)) {
            $params['col']['accid'] = $accid;
        }
        if (!is_null($appid)) {
            $params['col']['appid'] = $appid;
        }
        if (!is_null($rid)) {
            $params['col']['rid'] = $rid;
        }
        if (!is_null($order_by)) {
            $params['order_by'] = $order_by;
        }
        if (!is_null($direction)) {
            $params['direction'] = $direction;
        }

        try {
            $userRoles = $this->userRoleMapper->findForUidWithFilter($currentUser->getUid(), $params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $result = [];
        foreach ($userRoles as $userRole) {
            $result[] = $userRole->dump();
        }

        return new DataContainer($result, 'array');
    }
}
