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
use ApiOpenStudio\Core;
use ApiOpenStudio\Db;
use Monolog\Logger;

/**
 * Class ApplicationRead
 *
 * Processor class to fetch an application.
 */
class ApplicationRead extends Core\ProcessorEntity
{
    /**
     * Application mapper class.
     *
     * @var Db\ApplicationMapper
     */
    protected Db\ApplicationMapper $applicationMapper;

    /**
     * User role mapper class.
     *
     * @var Db\UserRoleMapper
     */
    protected Db\UserRoleMapper $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    protected Db\UserMapper $userMapper;

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
            'accountId' => [
                // phpcs:ignore
                'description' => 'Account ID to fetch to filter by. NULL or empty will not filter by account.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'applicationId' => [
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
            'orderBy' => [
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
     * @param Logger $logger Logger object.
     *
     * @throws Core\ApiException
     */
    public function __construct($meta, &$request, ADOConnection $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
        $accountId = $this->val('accountId', true);
        $applicationId = $this->val('applicationId', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('orderBy', true);
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

        $applications = $this->applicationMapper->findByUid($uid, $params);

        $result = [];
        foreach ($applications as $application) {
            $result[$application->getAppid()] = [
                'accid' => $application->getAccid(),
                'appid' => $application->getAppid(),
                'name' => $application->getName(),
            ];
        }

        return new Core\DataContainer($result, 'array');
    }
}
