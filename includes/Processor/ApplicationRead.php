<?php

/**
 * Class ApplicationRead.
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
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use stdClass;

/**
 * Class ApplicationRead
 *
 * Processor class to fetch an application.
 */
class ApplicationRead extends ProcessorEntity
{
    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    protected UserRoleMapper $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    protected UserMapper $userMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Application read',
        'machineName' => 'application_read',
        'description' => 'Fetch a single or multiple applications.',
        'menu' => 'Admin',
        'input' => [
            'account_id' => [
                // phpcs:ignore
                'description' => 'Account ID to fetch to filter by. NULL or empty will not filter by account.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'application_id' => [
                // phpcs:ignore
                'description' => 'Application ID to filter by. NULL or empty will not filter by application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                'description' => 'Application keyword to filter by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'order_by' => [
                'description' => 'Order by column.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['accid', 'appid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => '',
            ],
        ],
    ];

    /**
     * ApplicationRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->applicationMapper = new ApplicationMapper($this->db, $logger);
        $this->userRoleMapper = new UserRoleMapper($this->db, $logger);
        $this->userMapper = new UserMapper($this->db, $logger);
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

        $uid = Utilities::getUidFromToken();
        $accountId = $this->val('account_id', true);
        $applicationId = $this->val('application_id', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        // Filter params.
        $params = [];
        if (!empty($accountId)) {
            $params['filter'][] = [
                'keyword' => $accountId,
                'column' => 'accid',
            ];
        }
        if (!empty($applicationId)) {
            $params['filter'][] = [
                'keyword' => $applicationId,
                'column' => 'appid',
            ];
        }
        if (!empty($keyword)) {
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

        try {
            $applications = $this->applicationMapper->findByUid($uid, $params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $result = [];
        foreach ($applications as $application) {
            $result[$application->getAppid()] = [
                'accid' => $application->getAccid(),
                'appid' => $application->getAppid(),
                'name' => $application->getName(),
            ];
            if (empty($application->getOpenapi())) {
                $result[$application->getAppid()]['openapi'] = new stdClass();
            } else {
                $result[$application->getAppid()]['openapi'] = json_decode($application->getOpenapi());
            }
        }

        return new DataContainer($result, 'array');
    }
}
