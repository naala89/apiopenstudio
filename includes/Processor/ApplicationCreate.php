<?php
/**
 * Class ApplicationCreate.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Db;
use Monolog\Logger;

/**
 * Class ApplicationCreate
 *
 * Processor class to create an application.
 */
class ApplicationCreate extends Core\ProcessorEntity
{
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
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Application create',
        'machineName' => 'application_create',
        'description' => 'Create an application.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The parent account ID for the application.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The application name.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ApplicationCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Logger $logger Logger object.
     *
     * @throws ApiException
     */
    public function __construct($meta, &$request, ADOConnection $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
        $this->accountMapper = new Db\AccountMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
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
        $accid = $this->val('accid', true);
        $name = $this->val('name', true);

        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
        ) {
            throw new ApiException('Permission denied.', 6, $this->id, 417);
        }

        if (preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new Core\ApiException(
                "Invalid application name: $name. Only underscore, hyphen or alphanumeric characters permitted.",
                6,
                $this->id,
                400
            );
        }

        $account = $this->accountMapper->findByAccid($accid);
        if (empty($account->getAccid())) {
            throw new ApiException('Account does not exist: "' . $accid . '"', 6, $this->id, 400);
        }
        $application = $this->applicationMapper->findByAccidAppname($accid, $name);
        if (!empty($application->getAppid())) {
            throw new ApiException(
                "Application already exists ($name in account: " . $account->getName() . ")",
                6,
                $this->id,
                400
            );
        }


        $application = new Db\Application(null, $accid, $name);
        return new Core\DataContainer($this->applicationMapper->save($application), 'boolean');
    }
}
