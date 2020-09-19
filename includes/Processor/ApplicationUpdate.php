<?php
/**
 * Class ApplicationUpdate.
 *
 * @package    Gaterdata
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;
use Monolog\Logger;

/**
 * Class ApplicationUpdate
 *
 * Processor class to update an application.
 */
class ApplicationUpdate extends Core\ProcessorEntity
{
    /**
     * Account mapper class.
     *
     * @var Db\AccountMapper
     */
    protected $accountMapper;

    /**
     * Application mapper class.
     *
     * @var Db\ApplicationMapper
     */
    protected $applicationMapper;

    /**
     * User role mapper class.
     *
     * @var Db\UserRoleMapper
     */
    protected $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Application update',
        'machineName' => 'application_update',
        'description' => 'Update an application.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'Request token of the user making the call.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 0,
            ],
            'appid' => [
                'description' => 'The application iD.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ApplicationUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new Db\AccountMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|boolean|Core\Error Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $user = $this->userMapper->findBytoken($token);
        $appid = $this->val('appid', true);
        $accid = $this->val('accid', true);
        $name = $this->val('name', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application->getAccid())) {
            throw new ApiException("Application ID does not exist: $appid", 6, $this->id, 417);
        }

        if (!$this->userRoleMapper->hasRole($user->getUid(), 'Administrator')) {
            if ((
                    !empty($accid)
                    && $this->userRoleMapper->findByUidAppidRolename($user->getUid(), $appid, 'Account manager')
                )
                && !$this->userRoleMapper->findByUidAppidRolename(
                    $user->getUid(),
                    $application->getAccid(),
                    'Account manager'
                )) {
                throw new ApiException("Permission denied.", 6, $this->id, 417);
            }
        }

        if (!empty($accid)) {
            $account = $this->accountMapper->findByAccid($accid);
            if (empty($account->getAccid())) {
                throw new ApiException("Account ID does not exist: $accid", 6, $this->id, 417);
            }
            $application->setAccid($accid);
        }
        if (!empty($name)) {
            if (preg_match('/[^a-z_\-0-9]/i', $name)) {
                throw new Core\ApiException(
                    "Invalid application name: $name. Only underscore, hyphen or alhpanumeric characters permitted.",
                    6,
                    $this->id,
                    400
                );
            }
            $application->setName($name);
        }

        return $this->applicationMapper->save($application);
    }
}
