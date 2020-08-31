<?php
/**
 * Class InviteRead.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;
use Monolog\Logger;

/**
 * Class InviteRead
 *
 * Processor class fetch an invite.
 */
class InviteRead extends Core\ProcessorEntity
{
    /**
     * @var Db\InviteMapper
     */
    private $inviteMapper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Invite read',
        'machineName' => 'invite_read',
        'description' => 'Fetch a single or multiple invites.',
        'menu' => 'Admin',
        'input' => [
            'iid' => [
                'description' => 'Invite ID filter.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'Invite email filter.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'Column to order by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['iid', 'created', 'email', 'token'],
                'default' => 'created',
            ],
            'direction' => [
                'description' => 'Order direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['ASC', 'DESC', 'asc', 'desc'],
                'default' => 'DESC',
            ],
            'offset' => [
                'description' => 'Offset.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'limit' => [
                'description' => 'Limit.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * InviteRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->inviteMapper = new Db\InviteMapper($db);
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
        $invites = $this->inviteMapper->findAll($params);
        foreach ($invites as $invite) {
            $result[] = $invite->dump();
        }

        return new Core\DataContainer($result, 'array');
    }
}
