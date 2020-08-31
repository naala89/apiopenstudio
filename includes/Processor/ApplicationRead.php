<?php
/**
 * Class ApplicationRead.
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
 * Class ApplicationRead
 *
 * Processor class to fetch an application.
 */
class ApplicationRead extends Core\ProcessorEntity
{
    /**
     * @var Db\ApplicationMapper
     */
    protected $applicationMapper;

    /**
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Application read',
        'machineName' => 'application_read',
        'description' => 'Fetch a single or multiple applications.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'Token of the user making the call. This is used to limit access.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'accountId' => [
                // phpcs:ignore
                'description' => 'Account ID to fetch to filter by. NULL or empty will not filter by account.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'applicationId' => [
                // phpcs:ignore
                'description' => 'Application ID to filter by. NULL or empty will not filter by application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                'description' => 'Application keyword to filter by.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'orderBy' => [
                'description' => 'Order by column.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['accid', 'appid', 'name'],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Order by direction.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
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
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
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
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $user = $this->userMapper->findBytoken($token);
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

        if (!empty($token)) {
            $u = $this->userMapper->findBytoken($token);
            if (empty($user->getUid())) {
                throw new Core\ApiException('Invalid token.', 6, $this->id, 403);
            }
            $applications = $this->applicationMapper->findByUid($user->getUid(), $params);
        } else {
            $applications = $this->applicationMapper->findAll($params);
        }

        $result = [];
        foreach ($applications as $application) {
            $result[$application->getAppid()] = [
                'accid' =>$application->getAccid(),
                'appid' => $application->getAppid(),
                'name' => $application->getName(),
            ];
        }

        return new Core\DataContainer($result, 'array');
    }
}
