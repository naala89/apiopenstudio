<?php

/**
 * Class InviteRead.
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
use ApiOpenStudio\Db\InviteMapper;

/**
 * Class InviteRead
 *
 * Processor class fetch an invite.
 */
class InviteRead extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Invite read',
        'machineName' => 'invite_read',
        'description' => 'Fetch a single or multiple invites.',
        'menu' => 'Admin',
        'input' => [
            'iid' => [
                'description' => 'Invite ID filter.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'email' => [
                'description' => 'Invite email filter.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'order_by' => [
                'description' => 'Column to order by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['iid', 'created', 'email', 'token'],
                'default' => 'created',
            ],
            'direction' => [
                'description' => 'Order direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['ASC', 'DESC', 'asc', 'desc'],
                'default' => 'DESC',
            ],
            'offset' => [
                'description' => 'Offset.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'limit' => [
                'description' => 'Limit.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * Invite mapper class.
     *
     * @var InviteMapper
     */
    private InviteMapper $inviteMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->inviteMapper = new InviteMapper($db, $logger);
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

        $iid = $this->val('iid', true);
        $email = $this->val('email', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);
        $offset = $this->val('offset', true);
        $limit = $this->val('limit', true);

        $params = [];
        if (!empty($iid)) {
            $params['filter'][] = [
                'column' => 'iid',
                'value' => $iid,
                ];
        }
        if (!empty($email)) {
            $params['filter'][] = [
                'column' => 'email',
                'keyword' => $email,
            ];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }
        if (!empty($offset)) {
            $params['offset'] = $offset;
        }
        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        $result = [];
        try {
            $invites = $this->inviteMapper->findAll($params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        foreach ($invites as $invite) {
            $result[] = $invite->dump();
        }

        return new DataContainer($result, 'array');
    }
}
