<?php
/**
 * Class ResourceRead.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserMapper;
use Monolog\Logger;

/**
 * Class ResourceRead
 *
 * Processor class to fetch a resource.
 */
class ResourceRead extends Core\ProcessorEntity
{
    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource read',
        'machineName' => 'resource_read',
        'description' => 'List resources. If no appid/s ir resid is defined, all will be returned.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The token of the user making the call. This is used to limit the resources viewable',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
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
            'appid' => [
                'description' => 'The application IDs to filter by.',
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
                'limitValues' => ['appid', 'method', 'uri', 'name'],
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
     * ResourceRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
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
        $resid = $this->val('resid', true);
        $appid = $this->val('appid', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

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

        $result = $this->resourceMapper->findByUid($currentUser->getUid(), $params);
        if (empty($result)) {
            throw new Core\ApiException('No resources found', 6, $this->id);
        }

        $resources = [];
        foreach ($result as $item) {
            $resources[] = $item->dump();
        }

        return new Core\DataContainer($resources, 'array');
    }
}
