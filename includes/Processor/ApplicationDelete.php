<?php

/**
 * Class ApplicationDelete.
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
    protected Db\UserRoleMapper $userRoleMapper;

    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    protected Db\UserMapper $userMapper;

    /**
     * Application mapper class.
     *
     * @var Db\ApplicationMapper
     */
    protected Db\ApplicationMapper $applicationMapper;

    /**
     * Resource mapper class.
     *
     * @var Db\ResourceMapper
     */
    protected Db\ResourceMapper $resourceMapper;

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
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
        $appid = $this->val('applicationId', true);
        $application = $this->applicationMapper->findByAppid($appid);
        $accid = $application->getAccid();
        if (
            !$this->userRoleMapper->hasRole($uid, 'Administrator')
            && !$this->userRoleMapper->hasAccidRole($uid, $accid, 'Account manager')
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

        return new Core\DataContainer($this->applicationMapper->delete($application), 'boolean');
    }
}
