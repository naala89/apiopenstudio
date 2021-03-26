<?php

/**
 * Class ApplicationDelete.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Db;
use Monolog\Logger;

/**
 * Class ApplicationDelete
 *
 * Processor class to delete an application.
 */
class ApplicationDelete extends Core\ProcessorEntity
{
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
     * Application mapper class.
     *
     * @var Db\UserRoleMapper
     */
    protected $applicationMapper;

    /**
     * Resource mapper class.
     *
     * @var Db\ResourceMapper
     */
    protected $resourceMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Application delete',
        'machineName' => 'application_delete',
        'description' => 'Delete an application.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'Request token of the user making the call.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 0,
            ],
            'applicationId' => [
                'description' => 'The appication ID of the application.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ApplicationDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new Db\UserRoleMapper($this->db);
        $this->userMapper = new Db\UserMapper($this->db);
        $this->applicationMapper = new Db\ApplicationMapper($this->db);
        $this->resourceMapper = new Db\ResourceMapper($this->db);
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
        $appid = $this->val('applicationId', true);
        $application = $this->applicationMapper->findByAppid($appid);
        $accid = $application->getAccid();
        if (
            !$this->userRoleMapper->hasRole($user->getUid(), 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($user->getUid(), $accid, 'Account manager')
        ) {
            throw new ApiException("Permission denied.", 6, $this->id, 417);
        }
        if (empty($application->getAppid())) {
            throw new ApiException(
                "Delete application, invalid appid: $appid",
                6,
                $this->id,
                417
            );
        }

        $resources = $this->resourceMapper->findByAppId($appid);
        if (!empty($resources)) {
            throw new ApiException(
                "Cannot delete application, resources are assigned to this application: $appid",
                6,
                $this->id,
                417
            );
        }
        $userRoles = $this->userRoleMapper->findByFilter(['col' => ['appid' => $appid]]);
        if (!empty($userRoles)) {
            throw new ApiException(
                "Cannot delete application, users are assigned to this application: $appid",
                6,
                $this->id,
                417
            );
        }

        return $this->applicationMapper->delete($application);
    }
}
