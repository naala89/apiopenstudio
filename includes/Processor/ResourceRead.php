<?php

/**
 * Class ResourceRead.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\ResourceMapper;

/**
 * Class ResourceRead
 *
 * Processor class to fetch a resource.
 */
class ResourceRead extends ProcessorEntity
{
    /**
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    private ResourceMapper $resourceMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Resource read',
        'machineName' => 'resource_read',
        'description' => 'List resources. If no appid/s ir resid is defined, all will be returned.',
        'menu' => 'Admin',
        'input' => [
            'resid' => [
                'description' => 'The Resource ID to filter by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'appid' => [
                'description' => 'The application IDs to filter by.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['appid', 'method', 'uri', 'name'],
                'default' => 'appid',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'keyword' => [
                'description' => 'Keyword search',
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
     * ResourceRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
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

        $resid = $this->val('resid', true);
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);
        $uid = Utilities::getUidFromToken();

        $params = [];
        if (!empty($resid)) {
            $params['filter'][] = [
                'keyword' => $resid,
                'column' => 'resid',
            ];
        }
        if (!empty($appid)) {
            $params['filter'][] = [
                'keyword' => $appid,
                'column' => 'appid',
            ];
        }
        if (!empty($appid)) {
            $params['filter'][] = [
                'keyword' => "%$keyword%",
                'column' => 'name',
            ];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        $result = $this->resourceMapper->findByUid($uid, $params);
        if (empty($result)) {
            throw new ApiException('No resources found or insufficient privileges', 6, $this->id);
        }

        $resources = [];
        foreach ($result as $item) {
            $item = $item->dump();
            $item['openapi'] = json_decode($item['openapi'], true);
            $item['meta'] = json_decode($item['meta'], true);
            $resources[] = $item;
        }

        return new DataContainer($resources, 'array');
    }
}
