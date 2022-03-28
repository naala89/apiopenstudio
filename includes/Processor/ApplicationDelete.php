<?php

/**
 * Class ApplicationDelete.
 *
 * @package    ApiOpenStudio\Processor
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
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class ApplicationDelete
 *
 * Processor class to delete an application.
 */
class ApplicationDelete extends ProcessorEntity
{
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
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    protected ResourceMapper $resourceMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Application delete',
        'machineName' => 'application_delete',
        'description' => 'Delete an application.',
        'menu' => 'Admin',
        'input' => [
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
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new UserRoleMapper($this->db, $logger);
        $this->userMapper = new UserMapper($this->db, $logger);
        $this->applicationMapper = new ApplicationMapper($this->db, $logger);
        $this->resourceMapper = new ResourceMapper($this->db, $logger);
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
        $appid = $this->val('applicationId', true);
        $application = $this->applicationMapper->findByAppid($appid);
        $accid = $application->getAccid();
        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
        ) {
            throw new ApiException("permission denied", 4, $this->id, 403);
        }
        if (empty($application->getAppid())) {
            throw new ApiException(
                "delete application, invalid appid: $appid",
                6,
                $this->id,
                400
            );
        }

        $resources = $this->resourceMapper->findByAppId($appid);
        if (!empty($resources)) {
            throw new ApiException(
                "cannot delete application, resources are assigned to this application: $appid",
                6,
                $this->id,
                400
            );
        }
        $userRoles = $this->userRoleMapper->findByFilter(['col' => ['appid' => $appid]]);
        if (!empty($userRoles)) {
            throw new ApiException(
                "cannot delete application, users are assigned to this application: $appid",
                6,
                $this->id,
                400
            );
        }


        return new DataContainer($this->applicationMapper->delete($application), 'boolean');
    }
}
