<?php

/**
 * Class ApplicationUpdate.
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
    protected Db\AccountMapper $accountMapper;

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
        'name' => 'Application update',
        'machineName' => 'application_update',
        'description' => 'Update an application.',
        'menu' => 'Admin',
        'input' => [
            'appid' => [
                'description' => 'The application iD.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new Db\AccountMapper($this->db, $logger);
        $this->applicationMapper = new Db\ApplicationMapper($this->db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db, $logger);
        $this->userMapper = new Db\UserMapper($this->db, $logger);
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
        $appid = $this->val('appid', true);
        $accid = $this->val('accid', true);
        $name = $this->val('name', true);

        $application = $this->applicationMapper->findByAppid($appid);
        if (empty($application->getAccid())) {
            throw new Core\ApiException("Application ID does not exist: $appid", 6, $this->id, 417);
        }

        if (!$this->userRoleMapper->hasRole($uid, 'Administrator')) {
            if (
                (
                    !empty($accid)
                    && $this->userRoleMapper->findByUidAppidRolename($uid, $appid, 'Account manager')
                )
                && !$this->userRoleMapper->findByUidAppidRolename(
                    $uid,
                    $application->getAccid(),
                    'Account manager'
                )
            ) {
                throw new Core\ApiException("Permission denied.", 6, $this->id, 417);
            }
        }

        if (!empty($accid)) {
            $account = $this->accountMapper->findByAccid($accid);
            if (empty($account->getAccid())) {
                throw new Core\ApiException("Account ID does not exist: $accid", 6, $this->id, 417);
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

        return new Core\DataContainer($this->applicationMapper->save($application), 'boolean');
    }
}
