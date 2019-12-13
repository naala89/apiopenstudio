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
            'all' => [
                'description' => 'Fetch all resources.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
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
                'limitTypes' => ['integer', 'string'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application IDs to filter by. Comma separated if Multiple.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer', 'string'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['accid', 'appid', 'method', 'uri'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => ['asc', 'desc'],
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

        $resid = $this->val('resid', true);
        $all = $this->val('all', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        if (!empty($resid)) {
            return $this->findByResid($resid);
        }

        $params = $this->generateParams($keyword, ['uri'], $orderBy, $direction);

        if ($all) {
            return $this->findAll($params);
        }

        $appids =  $this->generateApplicationFilter($accid, $appid);
        if (empty($appids)) {
            throw new Core\ApiException('No resources found', 6, $this->id, 400);
        }
        return $this->findByApplication($appids, $params);
    }

    /**
     * Fetch a resource by a resid.
     *
     * @param integer $resid
     *   A resource ID.
     *
     * @return array
     *   Associative array of a resource row.
     *
     * @throws Core\ApiException
     */
    private function findByResid($resid)
    {
        $resource = $this->resourceMapper->findId($resid);
        if (empty($resource->getResid())) {
            throw new Core\ApiException('Unknown resource', 6, $this->id, 400);
        }
        return $resource->dump();
    }

    /**
     * Find all resources.
     *
     * @param array
     *   SQL query params.
     *
     * @return array
     *   An array of associative arrays of a resource rows.
     *
     * @throws Core\ApiException
     */
    private function findAll($params)
    {
        $result = $this->resourceMapper->all($params);
        $resources = [];
        foreach ($result as $item) {
            $resources[] = $item->dump();
        }
        return $resources;
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
    private function findByApplication($appids, $params)
    {
        $result = $this->resourceMapper->findByAppId($appids, $params);
        $resources = [];
        foreach ($result as $item) {
            $resources[] = $item->dump();
        }
        return $resources;
    }

    /**
     * Generate the application filter list array.
     *
     * @param integer|string $accid
     *   Account ID or comma separated account IDs.
     * @param integer|string $appid
     *   Application ID or comma separated application IDs.
     *
     * @return array
     *   Application IDs.
     *
     * @throws Core\ApiException
     */
    private function generateApplicationFilter($accid, $appid)
    {
        if (empty($appid)) {
            $appids = [];
        } elseif (is_numeric($appid)) {
            $appids = [$appid];
        } else {
            $appids = explode(',', $appid);
        }

        if (empty($accid)) {
            $accids = [];
        }
        if (is_numeric($accid)) {
            $accids = [$accid];
        } else {
            $accids = explode(',', $accid);
        }

        foreach ($accids as $accid) {
            $applications = $this->applicationMapper->findByAccid($accid);
            foreach ($applications as $application) {
                $appid = $application->getAppId();
                if (!in_array($appid, $appids)) {
                    $appids[] = $appid;
                }
            }
        }

        return $appids;
    }
}
