<?php

/**
 * Fetch list of resources.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\ResourceMapper;

class ResourceRead extends Core\ProcessorEntity
{
    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var ApplicationMapper
     */
    private $applicationMapper;

    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource read',
        'machineName' => 'resource_read',
        'description' => 'List resources. If no appid/s ir resid is defined, all will be returned.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'User ID of the user making the call. This is used to limit the resources viewable',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'resid' => [
                'description' => 'The Resource ID to filter by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'accid' => [
                'description' => 'The account IDs to filter by. Comma separated if Multiple.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application IDs to filter by. Comma separated if Multiple.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['appid', 'method', 'uri'],
                'default' => 'appid',
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
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->accountMapper = new AccountMapper($db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $resid = $this->val('resid', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $result = $this->findResources($uid, 'Developer', $accid, $appid, $resid, $keyword, $orderBy, $direction);
        $resources = [];
        foreach ($result as $item) {
            $resources[] = $item->dump();
        }

        return new Core\DataContainer($resources, 'array');
    }

    /**
     * Find all resources belonging to applications.
     *
     * @param array
     *   Application IDs.
     * @param array
     *   SQL query params.
     *
     * @return array
     *   An array of associative arrays of a resource rows.
     *
     * @throws Core\ApiException
     */
    private function findResources($uid, $role, $accid, $appid, $resid, $keyword, $orderBy, $direction)
    {
        $params = [];
        $params['order_by'] = $orderBy;
        $params['direction'] = $direction;
        if (!empty($keyword)) {
            $params['filter'][] = [
                'keyword' => "%$keyword%",
                'column' => "uri",
            ];
        }
        $rid = $this->roleMapper->findByName($role)->getRid();
        return $this->resourceMapper->findByUidRidAccidAppidResid($uid, $rid, $accid, $appid, $resid, $params);
    }
}
